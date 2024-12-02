<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsenceRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Comment out permission checks until Spatie package is properly installed
        /*
        $this->middleware('can:view absences');
        $this->middleware('can:create absences')->only(['create', 'store']);
        $this->middleware('can:update absences')->only(['edit', 'update', 'extend']);
        $this->middleware('can:delete absences')->only('destroy');
        */
    }

    public function index()
    {
        $user = Auth::user();
        $query = AbsenceRecord::with(['user.employee.department', 'recordedBy']);

        // Temporarily allow all authenticated users to view absences
        // if (!$user->can('view all absences')) {
        //     $query->whereHas('user.employee', function ($q) use ($user) {
        //         $q->where('supervisor_id', $user->id);
        //     });
        // }

        $absences = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('absences.index', compact('absences'));
    }

    public function create()
    {
        $user = Auth::user();

        // Temporarily allow access to all employees
        $employees = User::whereHas('employee')->get();

        // Original permission-based code
        /*
        if ($user->can('view all absences')) {
            $employees = User::whereHas('employee')->get();
        } else {
            $employees = User::whereHas('employee', function ($query) use ($user) {
                $query->where('supervisor_id', $user->id);
            })->get();
        }
        */

        return view('absences.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Temporarily disable permission check
        /*
        if (!$user->can('view all absences')) {
            $employee = User::findOrFail($request->user_id);
            if ($employee->employee->supervisor_id !== $user->id) {
                abort(403);
            }
        }
        */

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'type' => 'required|in:sick,personal,other',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000'
        ]);

        $absence = new AbsenceRecord($validated);
        $absence->recorded_by = $user->id;
        $absence->save();

        return redirect()->route('absences.index')
            ->with('success', 'Absence record created successfully.');
    }

    /**
     * Display absence patterns for a user.
     *
     * @param User $user The user whose patterns to view
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function patterns(User $user)
    {
        $this->authorize('viewPatterns', [$user]);

        $absencePatterns = AbsenceRecord::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        return view('absences.patterns', compact('user', 'absencePatterns'));
    }

    public function extend(Request $request, User $user)
    {
        $this->authorize('update', [AbsenceRecord::class, $user]);

        // Add extend logic here
    }
}
