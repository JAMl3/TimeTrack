<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ClockController;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AbsenceRecordController;
use App\Http\Controllers\HolidayManagementController;

Route::get('/', function () {
    return view('auth/login');
});

// Clock In/Out Routes - Public access
Route::get('/clock', [ClockController::class, 'index'])->name('clock.index');
Route::post('/clock', [ClockController::class, 'clockInOut'])->name('clock.clockInOut');
Route::get('/clock/change-pin', [ClockController::class, 'changePin'])->name('clock.change-pin');
Route::post('/clock/update-pin', [ClockController::class, 'updatePin'])->name('clock.update-pin');

// Test route - Only in local environment
if (app()->environment('local')) {
    Route::get('/clock/test', [ClockController::class, 'test'])->name('clock.test');
}

// Employee suggestion endpoint - Public access
Route::get('/employees/suggest', function (Request $request) {
    $term = $request->query('term');
    Log::info('Employee search request', ['term' => $term]);

    if (!$term || strlen($term) < 2) {
        Log::info('Search term too short');
        return response()->json(['employees' => []]);
    }

    try {
        $employees = Employee::where(function ($query) use ($term) {
            $query->where('employee_number', 'like', "%{$term}%")
                ->orWhereHas('user', function ($query) use ($term) {
                    $query->where('name', 'like', "%{$term}%");
                });
        })
        ->with('user')
        ->take(5)
        ->get()
        ->map(function ($employee) {
            return [
                'id' => $employee->id,
                'employee_number' => $employee->employee_number,
                'name' => $employee->user->name,
            ];
        });

        Log::info('Employee search results', ['count' => $employees->count()]);
        return response()->json(['employees' => $employees]);
    } catch (\Exception $e) {
        Log::error('Employee search error', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'An error occurred while searching'], 500);
    }
})->name('employees.suggest');

Route::middleware(['auth'])->group(function () {
    // Dashboard Route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Employee Management Routes
    Route::resource('employees', EmployeeController::class);

    // Report Routes
    Route::middleware('can:view reports')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
        Route::post('/reports/basic-clocking', [ReportController::class, 'basicClocking'])->name('reports.basic-clocking');
        Route::post('/reports/timesheet', [ReportController::class, 'timesheet'])->name('reports.timesheet');
        Route::post('/reports/department-overview', [ReportController::class, 'departmentOverview'])->name('reports.department-overview');
        Route::post('/reports/holiday-summary', [ReportController::class, 'holidaySummary'])->name('reports.holiday-summary');
        Route::post('/reports/absence-patterns', [ReportController::class, 'absencePatterns'])->name('reports.absence-patterns');
        Route::post('/reports/extended-absences', [ReportController::class, 'extendedAbsences'])->name('reports.extended-absences');
    });

    // Holiday Management Routes
    Route::prefix('holidays')->name('holidays.')->group(function () {
        // Routes for all users with view holidays permission
        Route::middleware('can:view holidays')->group(function () {
            Route::get('/', [HolidayManagementController::class, 'dashboard'])->name('dashboard');
            Route::get('/requests', [HolidayManagementController::class, 'listRequests'])->name('requests.index');
        });

        // Routes for users who can create holidays
        Route::middleware('can:create holidays')->group(function () {
            Route::get('/requests/create', [HolidayManagementController::class, 'createRequest'])->name('requests.create');
            Route::post('/requests', [HolidayManagementController::class, 'storeRequest'])->name('requests.store');
        });

        // Routes for users who can approve/reject holidays
        Route::middleware('can:approve holidays')->group(function () {
            Route::patch('/requests/{holidayRequest}/status', [HolidayManagementController::class, 'updateRequestStatus'])
                ->name('requests.update-status');
        });

        // Routes for managing entitlements
        Route::middleware('can:create entitlements')->group(function () {
            Route::get('/entitlements', [HolidayManagementController::class, 'listEntitlements'])->name('entitlements.index');
            Route::get('/entitlements/create', [HolidayManagementController::class, 'createEntitlement'])->name('entitlements.create');
            Route::post('/entitlements', [HolidayManagementController::class, 'storeEntitlement'])->name('entitlements.store');
        });
    });

    // Absence Routes
    Route::middleware('can:view absences')->group(function () {
        Route::resource('absences', AbsenceRecordController::class);
        Route::get('absences/patterns/{user}', [AbsenceRecordController::class, 'patterns'])
            ->name('absences.patterns');
        Route::post('absences/{user}/extend', [AbsenceRecordController::class, 'extend'])
            ->name('absences.extend');
    });

    // API Routes
    Route::middleware(['auth'])->prefix('api')->group(function () {
        Route::get('/departments/{department}/employees', [App\Http\Controllers\Api\DepartmentController::class, 'employees']);
    });
});

require __DIR__ . '/auth.php';
