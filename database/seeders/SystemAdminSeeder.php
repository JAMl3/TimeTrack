<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        $user = User::create([
            'name' => 'System Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // Create employee record for admin
        Employee::create([
            'user_id' => $user->id,
            'employee_number' => 'ADMIN001',
            'department_id' => 1,
            'position' => 'System Administrator',
            'pin_code' => Hash::make('1234'),
            'pin_changed' => true,
            'start_date' => now(),
            'employment_status' => 'active',
            'status' => 'active',
            'phone' => '0000000000',
            'company' => 'System',
            'branch' => 'Head Office',
            'shift_pattern' => [
                'monday' => ['start_time' => '09:00', 'end_time' => '17:00'],
                'tuesday' => ['start_time' => '09:00', 'end_time' => '17:00'],
                'wednesday' => ['start_time' => '09:00', 'end_time' => '17:00'],
                'thursday' => ['start_time' => '09:00', 'end_time' => '17:00'],
                'friday' => ['start_time' => '09:00', 'end_time' => '17:00']
            ]
        ]);
    }
}
