<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\DB;

class TestSystemNotification extends Command
{
    protected $signature = 'test:system-notification';
    protected $description = 'Send a test system notification';

    public function handle()
    {
        $this->info('Testing notification system...');

        try {
            DB::beginTransaction();

            // Find or create admin user
            $admin = User::where('email', 'admin@ringautomotive.com')->first();
            if (!$admin) {
                $this->error('Admin user not found!');
                return 1;
            }
            $this->info('✓ Found admin user: ' . $admin->name);

            // Update admin employee to have HR role
            $adminEmployee = Employee::where('user_id', $admin->id)->first();
            if ($adminEmployee) {
                $adminEmployee->update(['role' => 'hr']);
                $this->info('✓ Updated admin employee role to HR');
            } else {
                $this->error('Admin employee record not found!');
                return 1;
            }

            // Send test notification
            $admin->notify(new SystemNotification('This is a test notification. The notification system is working!'));
            $this->info('✓ Test notification sent to: ' . $admin->name);

            // Verify notification in database
            $notification = DB::table('notifications')
                ->where('notifiable_id', $admin->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($notification) {
                $this->info('✓ Notification found in database');
                $this->info('Notification ID: ' . $notification->id);
                $this->info('Notification Type: ' . $notification->type);
                $this->info('Notification Data: ' . $notification->data);
                $this->info('Created At: ' . $notification->created_at);
            } else {
                $this->error('Notification not found in database!');
                $this->info('Checking all notifications...');
                $allNotifications = DB::table('notifications')->get();
                $this->info('Total notifications in database: ' . $allNotifications->count());
                foreach ($allNotifications as $n) {
                    $this->info('---');
                    $this->info('ID: ' . $n->id);
                    $this->info('Type: ' . $n->type);
                    $this->info('Data: ' . $n->data);
                    $this->info('Created At: ' . $n->created_at);
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
