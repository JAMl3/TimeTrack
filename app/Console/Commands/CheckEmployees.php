<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;

class CheckEmployees extends Command
{
    protected $signature = 'check:employees';
    protected $description = 'Check employee data';

    public function handle()
    {
        $employees = Employee::with('user')->get();

        foreach ($employees as $employee) {
            $this->info('-------------------');
            $this->info('Employee Number: ' . $employee->employee_number);
            $this->info('Name: ' . $employee->user->name);
            $this->info('Email: ' . $employee->user->email);
            $this->info('PIN Code: ' . $employee->pin_code);
            $this->info('PIN Changed: ' . ($employee->pin_changed ? 'Yes' : 'No'));
            $this->info('Role: ' . $employee->role);
        }
    }
}
