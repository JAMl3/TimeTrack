<?php

namespace App\Http\Controllers;

use App\Models\HolidayEntitlement;
use App\Models\HolidayRequest;
use App\Models\User;
use App\Models\AbsenceRecord;
use App\Notifications\HolidayRequestStatusChanged;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class HolidayManagementController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('can:view holidays');
        $this->middleware('can:create holidays')->only(['createRequest', 'storeRequest']);
        $this->middleware('can:approve holidays')->only('updateRequestStatus');
        $this->middleware('can:create entitlements')->only(['listEntitlements', 'createEntitlement', 'storeEntitlement']);
    }

    // Dashboard view showing combined holiday information
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->get('tab', 'requests');
        $search = $request->get('search');
        $status = $request->get('status');

        $requests = $this->getFilteredHolidayRequests($user, $search, $status);
        $entitlements = $this->getFilteredEntitlements($user, $search);
        $absences = $this->getFilteredAbsences($user, $search);

        return view('holidays.dashboard', compact('requests', 'entitlements', 'absences', 'activeTab'));
    }

    // Holiday Request Methods
    public function listRequests()
    {
        $user = Auth::user();
        $requests = $this->getFilteredHolidayRequests($user);
        return view('holidays.requests.index', compact('requests'));
    }

    public function createRequest()
    {
        return view('holidays.requests.create');
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:500',
        ]);

        $holidayRequest = new HolidayRequest($validated);
        $holidayRequest->user_id = Auth::id();
        $holidayRequest->status = 'pending';
        $holidayRequest->save();

        $this->notifySupervisors($holidayRequest);

        return redirect()->route('holidays.dashboard')->with('success', 'Holiday request submitted successfully.');
    }

    public function updateRequestStatus(Request $request, HolidayRequest $holidayRequest)
    {
        $this->authorize('update', $holidayRequest);

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $holidayRequest) {
            $holidayRequest->status = $validated['status'];
            $holidayRequest->rejection_reason = $validated['rejection_reason'] ?? null;
            $holidayRequest->approved_by = Auth::id();
            $holidayRequest->save();

            if ($validated['status'] === 'approved') {
                $this->updateEntitlementForApprovedRequest($holidayRequest);
            }

            $holidayRequest->user->notify(new HolidayRequestStatusChanged($holidayRequest));
        });

        return redirect()->route('holidays.dashboard')->with('success', 'Holiday request updated successfully.');
    }

    // Holiday Entitlement Methods
    public function listEntitlements()
    {
        $user = Auth::user();
        $entitlements = $this->getFilteredEntitlements($user);
        return view('holidays.entitlements.index', compact('entitlements'));
    }

    public function createEntitlement()
    {
        $this->authorize('create', HolidayEntitlement::class);
        $employees = User::whereHas('employee')->get();
        return view('holidays.entitlements.create', compact('employees'));
    }

    public function storeEntitlement(Request $request)
    {
        $this->authorize('create', HolidayEntitlement::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'total_days' => 'required|integer|min:0',
            'year' => 'required|integer|min:' . now()->year,
            'carry_over_days' => 'nullable|integer|min:0',
            'carry_over_expiry' => 'nullable|date|after:today',
        ]);

        if ($this->entitlementExists($validated['user_id'], $validated['year'])) {
            return back()->withErrors(['user_id' => 'Holiday entitlement already exists for this user and year.']);
        }

        $entitlement = $this->createNewEntitlement($validated);

        return redirect()->route('holidays.dashboard')->with('success', 'Holiday entitlement created successfully.');
    }

    // Report Generation Methods
    public function generateReport(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:usage,patterns,department',
        ]);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);

        $data = match ($validated['report_type']) {
            'usage' => $this->generateUsageReport($startDate, $endDate),
            'patterns' => $this->generatePatternsReport($startDate, $endDate),
            'department' => $this->generateDepartmentReport($startDate, $endDate),
        };

        return view("holidays.reports.{$validated['report_type']}", $data);
    }

    // Private Helper Methods
    private function getFilteredHolidayRequests($user, $search = null, $status = null)
    {
        $query = HolidayRequest::with(['user', 'approver']);

        if ($user->hasRole(['admin', 'hr'])) {
            // No additional filters needed - can see all
        } elseif ($user->hasRole('supervisor')) {
            $teamMembers = User::where('supervisor_id', $user->id)->pluck('id');
            $query->whereIn('user_id', $teamMembers);
        } else {
            $query->where('user_id', $user->id);
        }

        return $query->when($search, function ($q) use ($search) {
            $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        })
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10);
    }

    private function getFilteredEntitlements($user, $search = null)
    {
        $query = HolidayEntitlement::with('user')->where('year', now()->year);

        if (!$user->hasRole(['admin', 'hr'])) {
            if ($user->hasRole('supervisor')) {
                $teamMembers = User::where('supervisor_id', $user->id)->pluck('id');
                $query->whereIn('user_id', $teamMembers);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        return $query->when($search, function ($q) use ($search) {
            $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        })
            ->latest()
            ->paginate(10);
    }

    private function getFilteredAbsences($user, $search = null)
    {
        $query = AbsenceRecord::with('user');

        if (!$user->hasRole(['admin', 'hr'])) {
            if ($user->hasRole('supervisor')) {
                $teamMembers = User::where('supervisor_id', $user->id)->pluck('id');
                $query->whereIn('user_id', $teamMembers);
            } else {
                $query->where('user_id', $user->id);
            }
        }

        return $query->when($search, function ($q) use ($search) {
            $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
        })
            ->latest()
            ->paginate(10);
    }

    private function notifySupervisors($holidayRequest)
    {
        $supervisors = User::whereHas('roles', fn($q) => $q->where('name', 'supervisor'))->get();
        Notification::send($supervisors, new HolidayRequestStatusChanged($holidayRequest));
    }

    private function updateEntitlementForApprovedRequest($holidayRequest)
    {
        $entitlement = $holidayRequest->user->currentHolidayEntitlement;
        if ($entitlement) {
            $days = $holidayRequest->start_date->diffInDaysExcludingWeekends($holidayRequest->end_date) + 1;
            $entitlement->days_taken += $days;
            $entitlement->days_remaining = $entitlement->total_days - $entitlement->days_taken;
            $entitlement->save();
        }
    }

    private function entitlementExists($userId, $year)
    {
        return HolidayEntitlement::where('user_id', $userId)
            ->where('year', $year)
            ->exists();
    }

    private function createNewEntitlement($data)
    {
        $entitlement = new HolidayEntitlement();
        $entitlement->user_id = $data['user_id'];
        $entitlement->total_days = $data['total_days'];
        $entitlement->year = $data['year'];
        $entitlement->carry_over_days = $data['carry_over_days'] ?? 0;
        $entitlement->carry_over_expiry = $data['carry_over_expiry'] ?? null;
        $entitlement->days_taken = 0;
        $entitlement->days_remaining = $data['total_days'] + ($data['carry_over_days'] ?? 0);
        $entitlement->save();
        return $entitlement;
    }
}
