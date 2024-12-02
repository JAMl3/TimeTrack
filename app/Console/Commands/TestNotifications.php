<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\TimeLog;
use App\Models\User;
use App\Models\Department;
use App\Notifications\LateArrivalNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class TestNotifications extends Command
{
    protected $signature = 'test:notifications';
    protected $description = 'Test the notification system';

    public function handle()
    {
        $this->info('Testing notification system...');

        try {
            DB::beginTransaction();

            // Find or create test employee
            $employee = Employee::with('user')->where('employee_number', 'EMP0002')->first();
            if (!$employee) {
                $this->error('Employee EMP0002 not found');
                return 1;
            }

            $this->info('✓ Using employee: ' . $employee->employee_number . ' (User ID: ' . $employee->user_id . ')');

            // Find or create HR supervisor
            $supervisor = Employee::with('user')->where('role', 'hr')->first();
            if (!$supervisor) {
                // Find or create IT department
                $department = Department::firstOrCreate(
                    ['code' => 'IT'],
                    [
                        'name' => 'Information Technology',
                        'description' => 'IT department'
                    ]
                );

                // Create HR supervisor
                $supervisorUser = User::create([
                    'name' => 'HR Supervisor',
                    'email' => 'hr@example.com',
                    'password' => Hash::make('password')
                ]);

                $supervisor = Employee::create([
                    'user_id' => $supervisorUser->id,
                    'employee_number' => 'HR001',
                    'role' => 'hr',
                    'status' => 'active',
                    'pin_code' => '1234',
                    'phone' => '1234567890',
                    'company' => 'Ring Automotive',
                    'department_id' => $department->id,
                    'position' => 'HR Manager',
                    'branch' => 'Leeds',
                    'start_date' => now(),
                    'shift_pattern' => [
                        'monday' => ['start' => '09:00', 'end' => '17:00'],
                        'tuesday' => ['start' => '09:00', 'end' => '17:00'],
                        'wednesday' => ['start' => '09:00', 'end' => '17:00'],
                        'thursday' => ['start' => '09:00', 'end' => '17:00'],
                        'friday' => ['start' => '09:00', 'end' => '17:00']
                    ]
                ]);

                $this->info('✓ Created new HR supervisor');
            }

            $employee->supervisor_id = $supervisor->id;
            $employee->save();

            $this->info('✓ Assigned supervisor: ' . $supervisor->user->name . ' (User ID: ' . $supervisor->user_id . ')');

            // Create a late time log
            $timeLog = TimeLog::create([
                'employee_id' => $employee->id,
                'clock_in' => now()->setTime(9, 30), // 9:30 AM is late for 7:00 AM start
                'status' => TimeLog::STATUS_PRESENT,
                'is_late' => true,
                'notes' => 'Late arrival: 150 minutes after shift start (07:00)'
            ]);

            $this->info('✓ Created late time log (ID: ' . $timeLog->id . ')');

            // Send notification
            $notification = new LateArrivalNotification($timeLog);
            $supervisor->user->notify($notification);
            $this->info('✓ Sent notification to supervisor');

            // Check notifications table immediately
            $this->info('Checking notifications table...');
            $latestNotification = DB::table('notifications')
                ->where('notifiable_id', $supervisor->user_id)
                ->where('type', 'App\Notifications\LateArrivalNotification')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($latestNotification) {
                $this->info('✓ Found late arrival notification:');
                $this->info('ID: ' . $latestNotification->id);
                $this->info('Type: ' . $latestNotification->type);
                $this->info('Data: ' . $latestNotification->data);
                $this->info('Created at: ' . $latestNotification->created_at);
            } else {
                $this->error('No late arrival notification found!');

                // Check all notifications for this user
                $this->info('All notifications for supervisor:');
                $allNotifications = DB::table('notifications')
                    ->where('notifiable_id', $supervisor->user_id)
                    ->orderBy('created_at', 'desc')
                    ->get();

                foreach ($allNotifications as $n) {
                    $this->info('---');
                    $this->info('ID: ' . $n->id);
                    $this->info('Type: ' . $n->type);
                    $this->info('Data: ' . $n->data);
                    $this->info('Created at: ' . $n->created_at);
                }
            }

            DB::commit();
            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
