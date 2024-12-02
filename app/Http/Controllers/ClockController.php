<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClockController extends Controller
{
    public function index()
    {
        return view('clock.index');
    }

    public function clockInOut(Request $request)
    {
        Log::info('Clock in/out request received', [
            'employee_number' => $request->employee_number,
            'has_pin' => !empty($request->pin_code),
            'all_data' => $request->all()
        ]);

        $request->validate([
            'employee_number' => 'required',
            'pin_code' => 'required|size:4',
            'status' => 'sometimes|string|in:' . implode(',', [
                TimeLog::STATUS_PRESENT,
                TimeLog::STATUS_ABSENT,
                TimeLog::STATUS_LEAVE
            ])
        ]);

        $employee = Employee::where('employee_number', $request->employee_number)
            ->where('status', 'active')
            ->first();

        if (!$employee || !Hash::check($request->pin_code, $employee->pin_code)) {
            return back()->with('error', 'Invalid employee number or PIN code.');
        }

        if (!$employee->pin_changed) {
            return redirect()->route('clock.change-pin', ['employee_number' => $employee->employee_number])
                ->with('warning', 'Please change your default PIN before clocking in.');
        }

        $existingLog = TimeLog::active()->where('employee_id', $employee->id)->first();

        try {
            return DB::transaction(function () use ($existingLog, $employee, $request) {
                if ($existingLog) {
                    // Clock out
                    $existingLog->clock_out = now();
                    $existingLog->save();
                    $existingLog->refresh();
                    $existingLog->checkShiftTiming();

                    return back()->with('success', 'Successfully clocked out at ' . $existingLog->clock_out->format('H:i'));
                } else {
                    // Clock in
                    $timeLog = new TimeLog();
                    $timeLog->employee_id = $employee->id;
                    $timeLog->clock_in = now();
                    $timeLog->status = TimeLog::STATUS_PRESENT;
                    $timeLog->is_late = false;
                    $timeLog->save();

                    $timeLog->refresh();
                    $timeLog->checkShiftTiming();

                    if ($timeLog->is_late) {
                        Log::info('Employee is late, saving to trigger notification');
                        $timeLog->save();
                    }

                    $message = 'Successfully clocked in at ' . $timeLog->clock_in->format('H:i');
                    if ($timeLog->is_late) {
                        $minutesLate = $timeLog->getMinutesLate();
                        $message .= " (Late by {$minutesLate} minutes)";
                    }

                    return back()->with('success', $message);
                }
            });
        } catch (\Exception $e) {
            Log::error('Clock in/out error', [
                'error' => $e->getMessage(),
                'employee_id' => $employee->id,
                'shift_pattern' => $employee->shift_pattern,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while processing your request. Please try again.');
        }
    }

    public function changePin(Request $request)
    {
        $employee = Employee::where('employee_number', $request->employee_number)
            ->where('status', 'active')
            ->firstOrFail();

        return view('clock.change-pin', compact('employee'));
    }

    public function updatePin(Request $request)
    {
        $request->validate([
            'employee_number' => 'required',
            'current_pin' => 'required|size:4',
            'new_pin' => 'required|size:4|different:current_pin',
            'new_pin_confirmation' => 'required|same:new_pin'
        ]);

        // First find the employee
        $employee = Employee::where('employee_number', $request->employee_number)
            ->where('status', 'active')
            ->firstOrFail();

        // Verify current PIN
        if (!Hash::check($request->current_pin, $employee->pin_code)) {
            return back()->withErrors(['current_pin' => 'The current PIN is incorrect.']);
        }

        try {
            $employee->update([
                'pin_code' => $request->new_pin,
                'pin_changed' => true
            ]);

            return redirect()->route('clock.index')
                ->with('success', 'PIN successfully changed. You can now clock in with your new PIN.');
        } catch (\Exception $e) {
            Log::error('PIN change error', [
                'error' => $e->getMessage(),
                'employee_id' => $employee->id,
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'An error occurred while changing your PIN. Please try again.');
        }
    }

    public function test()
    {
        try {
            DB::beginTransaction();

            // Find or create test employee
            $employee = Employee::where('employee_number', 'EMP0003')->first();
            if (!$employee) {
                return "Test employee EMP0003 not found. Please create one first.";
            }

            // Simulate late clock-in
            $timeLog = new TimeLog([
                'employee_id' => $employee->id,
                'clock_in' => now()->subHours(2), // 2 hours late
                'status' => TimeLog::STATUS_PRESENT,
                'is_late' => true
            ]);

            $timeLog->save();
            $timeLog->refresh();
            $timeLog->checkShiftTiming();

            // Simulate early departure
            $timeLog->clock_out = now();
            $timeLog->save();
            $timeLog->refresh();
            $timeLog->checkShiftTiming();

            DB::commit();

            return "Test completed successfully. Check notifications table for results.";
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Test error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return "Error: " . $e->getMessage();
        }
    }
}
