<?php

namespace App\Notifications\Channels;

use App\Services\Sms\SmsManager;
use Illuminate\Notifications\Notification;
use RuntimeException;

class SmsChannel
{
    public function __construct(protected SmsManager $sms)
    {
    }

    public function send(object $notifiable, Notification $notification): void
    {
        $to = $notifiable->routeNotificationFor('sms', $notification);

        if (! $to) {
            throw new RuntimeException('Notifiable has no phone number for SMS.');
        }

        $salon = $notifiable->salon ?? null;

        if (! $salon) {
            throw new RuntimeException('Notifiable has no salon context for SMS.');
        }

        $this->sms->send($salon, $to, $notification->toSms($notifiable));
    }
}
