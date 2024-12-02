<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestSystemNotification extends Notification
{
    use Queueable;

    public function __construct() {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => 'This is a test notification for the System Administrator',
            'type' => 'test',
            'created_at' => now()
        ];
    }
}
