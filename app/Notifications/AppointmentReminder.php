<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Notifications\Channels\SmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification
{
    use Queueable;

    /**
     * @param 'mail'|'sms' $channel  Sent one channel at a time so each can be
     *        tracked and retried independently in appointment_reminders.
     */
    public function __construct(public Appointment $appointment, public string $channel)
    {
    }

    public function via(object $notifiable): array
    {
        return match ($this->channel) {
            'mail' => ['mail'],
            'sms'  => [SmsChannel::class],
            default => [],
        };
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment;
        $salonName   = $appointment->salon?->name ?? config('app.name');

        return (new MailMessage)
            ->subject("Appointment Reminder — {$salonName}")
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('This is a reminder of your upcoming appointment:')
            ->line('**' . $appointment->service?->name . '** on ' . $appointment->start->format('l, d M Y \a\t H:i'))
            ->line('With ' . ($appointment->staffProfile?->user?->name ?? 'our team') . ' at ' . $salonName . '.')
            ->line('If you need to reschedule or cancel, please contact us as soon as possible.');
    }

    public function toSms(object $notifiable): string
    {
        $appointment = $this->appointment;
        $salonName   = $appointment->salon?->name ?? config('app.name');

        return "Reminder: your appointment for {$appointment->service?->name} is on "
            . $appointment->start->format('D d/m \a\t H:i') . " at {$salonName}.";
    }
}
