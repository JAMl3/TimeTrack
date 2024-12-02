<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeLog;
use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total number of employees
        $totalEmployees = Employee::count();

        // Get number of departments
        $departments = Department::count();

        // Initialize variables
        $activeShifts = 0;
        $todayActivity = 0;
        $todayLogs = collect();

        // Only query time_logs if the table exists
        if (Schema::hasTable('time_logs')) {
            // Get number of active shifts (employees currently clocked in)
            $activeShifts = TimeLog::whereNull('clock_out')
                ->whereDate('clock_in', today())
                ->count();

            // Get today's activity count
            $todayActivity = TimeLog::whereDate('clock_in', today())->count();

            // Get today's logs with employee and department information
            $todayLogs = TimeLog::with(['employee.user', 'employee.department'])
                ->whereDate('clock_in', today())
                ->orderBy('clock_in', 'desc')
                ->paginate(10);
        }

        return view('dashboard', compact(
            'totalEmployees',
            'activeShifts',
            'departments',
            'todayActivity',
            'todayLogs'
        ));
    }
}
