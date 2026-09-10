<?php

namespace App\Services\Sms;

use Throwable;

/**
 * Wraps whichever gateway driver a salon has configured (or NullSmsDriver, if none)
 * so every send attempt — regardless of provider, regardless of outcome — is
 * appended to the shared audit log. Not a "provider" itself; SmsManager always
 * wraps the resolved driver in this before returning it, so the log stays a
 * complete record even after a salon switches gateways.
 */
class LoggingSmsDriver implements SmsDriver
{
    public function __construct(
        protected SmsDriver $inner,
        protected string $senderId,
        protected string $logPath,
    ) {
    }

    public function send(string $to, string $message, array $context = []): void
    {
        try {
            $this->inner->send($to, $message, $context);
            $this->write($to, $context['salon_id'] ?? null, sent: true);
        } catch (Throwable $e) {
            // Logged as NOT SENT for any failure — a gateway-reported error, invalid
            // credentials, or a plain network/connection failure — so the log stays
            // a complete record regardless of what went wrong.
            $this->write($to, $context['salon_id'] ?? null, sent: false);

            throw $e;
        }
    }

    protected function write(string $to, ?int $salonId, bool $sent): void
    {
        $directory = dirname($this->logPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $line = sprintf(
            "[%s] sender_id=%s salon_id=%s receiver=%s status=%s\n",
            now()->toDateTimeString(),
            $this->senderId,
            $salonId ?? 'unknown',
            $to,
            $sent ? 'SENT' : 'NOT SENT'
        );

        file_put_contents($this->logPath, $line, FILE_APPEND | LOCK_EX);
    }
}
