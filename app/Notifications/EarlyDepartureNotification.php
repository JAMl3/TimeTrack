<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class EarlyDepartureNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $timeLog;

    public function __construct($timeLog)
    {
        $this->timeLog = $timeLog;
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $employee = $this->timeLog->employee;
        $minutesEarly = $this->timeLog->getMinutesEarly();

        return (new MailMessage)
            ->subject('Early Departure Notification')
            ->line("Employee {$employee->user->name} ({$employee->employee_number}) has left early.")
            ->line("Clock out time: " . $this->timeLog->clock_out->format('H:i'))
            ->line("Minutes early: {$minutesEarly}")
            ->action('View Details', url('/dashboard'));
    }

    public function toArray($notifiable): array
    {
        $employee = $this->timeLog->employee;
        $minutesEarly = $this->timeLog->getMinutesEarly();

        return [
            'message' => "Employee {$employee->user->name} ({$employee->employee_number}) left {$minutesEarly} minutes early at " . $this->timeLog->clock_out->format('H:i'),
            'time' => $this->timeLog->clock_out,
            'type' => 'early_departure',
            'employee_id' => $employee->id,
            'time_log_id' => $this->timeLog->id
        ];
    }
}
