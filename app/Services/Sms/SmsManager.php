<?php

namespace App\Services\Sms;

use App\Models\Salon;
use App\Services\Sms\Drivers\CytaSmsDriver;
use App\Services\Sms\Drivers\NullSmsDriver;

/**
 * Resolves the right SMS gateway per salon — each salon picks its own provider and
 * supplies its own account details from Settings (App\Models\Salon::sms_driver /
 * sms_credentials), rather than one global gateway for the whole platform. Every
 * resolved driver is wrapped in LoggingSmsDriver so sends are auditable regardless
 * of provider or outcome.
 *
 * Onboarding a new provider (Epic, Cablenet, Primetel, or a foreign gateway for a
 * future country) means one new case in resolveInner() plus one new class under
 * Drivers/ — nothing that calls SmsManager::send() has to change.
 */
class SmsManager
{
    public function driverFor(Salon $salon): SmsDriver
    {
        return new LoggingSmsDriver(
            inner: $this->resolveInner($salon),
            senderId: config('services.sms.sender_id', config('app.name')),
            logPath: config('services.sms.log.path', storage_path('logs/sms.log')),
        );
    }

    protected function resolveInner(Salon $salon): SmsDriver
    {
        $credentials = $salon->sms_credentials ?? [];

        return match ($salon->sms_driver) {
            'cyta' => new CytaSmsDriver(
                username: $credentials['username'] ?? null,
                secretKey: $credentials['secret_key'] ?? null,
                language: $credentials['language'] ?? 'en',
            ),
            default => new NullSmsDriver(),
        };
    }

    public function send(Salon $salon, string $to, string $message, array $context = []): void
    {
        $this->driverFor($salon)->send($to, $message, array_merge($context, ['salon_id' => $salon->id]));
    }
}
