<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Employee suggestion endpoint - remove auth middleware to allow public access
Route::get('/employees/suggest', function (Request $request) {
    $number = $request->query('number');
    Log::info('Employee suggestion request', ['number' => $number]);

    if (!$number) {
        return response()->json(['employee' => null]);
    }

    try {
        $employee = Employee::with('user')
            ->where('employee_number', 'like', $number . '%')
            ->first();

        if ($employee && $employee->user) {
            return response()->json([
                'employee' => [
                    'name' => $employee->user->name,
                    'employee_number' => $employee->employee_number,
                ]
            ]);
        }

        return response()->json(['employee' => null]);
    } catch (\Exception $e) {
        Log::error('Error in employee suggestion', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Internal server error'], 500);
    }
});
