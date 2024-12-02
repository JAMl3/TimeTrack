<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TestNotification extends Command
{
    protected $signature = 'test:notification';
    protected $description = 'Test the notification system';

    public function handle()
    {
        $this->info('Testing notification system...');

        try {
            $admin = User::where('email', 'admin@ringautomotive.com')->first();
            if (!$admin) {
                $this->error('Admin user not found!');
                return 1;
            }
            $this->info('✓ Found admin user: ' . $admin->name);

            // Direct database insert for testing
            $notificationId = Str::uuid();
            DB::table('notifications')->insert([
                'id' => $notificationId,
                'type' => 'App\Notifications\SystemNotification',
                'notifiable_type' => get_class($admin),
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'message' => 'This is a test notification. If you can see this, the notification system is working!',
                    'type' => 'test',
                    'created_at' => now()
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->info('✓ Notification created successfully');
            $this->info('Notification ID: ' . $notificationId);

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
