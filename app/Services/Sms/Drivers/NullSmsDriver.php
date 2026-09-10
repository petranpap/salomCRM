<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsDriver;
use App\Services\Sms\SmsSendException;

/**
 * Used whenever a salon hasn't configured a real SMS gateway yet — the default
 * until an owner picks a provider in Settings and fills in its account details.
 * Never contacts anything; always fails, which LoggingSmsDriver (every send goes
 * through it) records as NOT SENT.
 */
class NullSmsDriver implements SmsDriver
{
    public function send(string $to, string $message, array $context = []): void
    {
        throw new SmsSendException('No SMS gateway is configured for this salon yet.');
    }
}
