<?php

namespace App\Notifications;

use App\Models\HolidayRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HolidayRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $holidayRequest;

    public function __construct(HolidayRequest $holidayRequest)
    {
        $this->holidayRequest = $holidayRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Holiday Request Status Updated')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your holiday request for ' . $this->holidayRequest->start_date->format('M d, Y') . ' to ' . $this->holidayRequest->end_date->format('M d, Y') . ' has been ' . $this->holidayRequest->status . '.');

        if ($this->holidayRequest->status === 'approved') {
            $message->line('Your request has been approved. Enjoy your time off!');
        } elseif ($this->holidayRequest->status === 'rejected') {
            $message->line('Unfortunately, your request has been rejected.')
                ->line('Reason: ' . ($this->holidayRequest->rejection_reason ?? 'No reason provided'));
        }

        return $message
            ->action('View Request', route('holidays.show', $this->holidayRequest))
            ->line('Thank you for using our application!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'holiday_request_id' => $this->holidayRequest->id,
            'status' => $this->holidayRequest->status,
            'start_date' => $this->holidayRequest->start_date->format('Y-m-d'),
            'end_date' => $this->holidayRequest->end_date->format('Y-m-d'),
            'rejection_reason' => $this->holidayRequest->rejection_reason,
        ];
    }
}
