<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Support\Facades\Hash;

class TestEmployeeSeeder extends Seeder
{
    public function run()
    {
        // Get the IT department
        $department = Department::where('code', 'IT')->firstOrFail();

        // Create user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@test.com',
            'password' => Hash::make('password'),
        ]);

        // Create employee
        Employee::create([
            'user_id' => $user->id,
            'employee_number' => 'EMP0001',
            'department_id' => $department->id,
            'position' => 'Developer',
            'pin_code' => '1234',
            'pin_changed' => false,
            'status' => 'active',
            'employment_status' => 'active',
            'phone' => '0000000000',
            'company' => 'Test Company',
            'branch' => 'Test Branch',
            'start_date' => now(),
            'shift_pattern' => [
                'monday' => ['start_time' => '09:00', 'end_time' => '17:00'],
                'tuesday' => ['start_time' => '09:00', 'end_time' => '17:00'],
                'wednesday' => ['start_time' => '09:00', 'end_time' => '17:00'],
                'thursday' => ['start_time' => '09:00', 'end_time' => '17:00'],
                'friday' => ['start_time' => '09:00', 'end_time' => '17:00']
            ],
        ]);
    }
}
