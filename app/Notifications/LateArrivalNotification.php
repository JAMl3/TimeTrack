<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class LateArrivalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $timeLog;

    public function __construct($timeLog)
    {
        Log::info('LateArrivalNotification constructed', [
            'time_log_id' => $timeLog->id,
            'employee_id' => $timeLog->employee_id
        ]);
        $this->timeLog = $timeLog;
    }

    public function via($notifiable): array
    {
        Log::info('Determining notification channels', [
            'notifiable_id' => $notifiable->id,
            'notifiable_type' => get_class($notifiable)
        ]);

        $channels = ['database'];
        Log::info('Available notification channels', ['channels' => $channels]);
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $employee = $this->timeLog->employee;
        $minutesLate = $this->timeLog->getMinutesLate();

        return (new MailMessage)
            ->subject('Late Arrival Notification')
            ->line("Employee {$employee->user->name} ({$employee->employee_number}) has arrived late.")
            ->line("Clock in time: " . $this->timeLog->clock_in->format('H:i'))
            ->line("Minutes late: {$minutesLate}")
            ->action('View Details', url('/dashboard'));
    }

    public function toDatabase($notifiable): array
    {
        $employee = $this->timeLog->employee;
        $minutesLate = $this->timeLog->getMinutesLate();

        return [
            'message' => "Employee {$employee->user->name} ({$employee->employee_number}) arrived {$minutesLate} minutes late at " . $this->timeLog->clock_in->format('H:i'),
            'time' => $this->timeLog->clock_in,
            'type' => 'late_arrival',
            'employee_id' => $employee->id,
            'time_log_id' => $this->timeLog->id,
            'minutes_late' => $minutesLate
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
