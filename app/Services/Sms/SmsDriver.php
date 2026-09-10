<?php

namespace App\Services\Sms;

/**
 * Contract every SMS gateway integration implements — Cyta today, other Cypriot
 * carriers (Epic, Cablenet, Primetel) or foreign providers (Twilio, Infobip, Vonage)
 * as the SaaS expands into new countries. Adding a provider means one new class here
 * plus one new `create*Driver()` method on SmsManager — nothing else in the app
 * (notifications, controllers) needs to know which gateway is actually sending.
 */
interface SmsDriver
{
    /**
     * @param  string  $to  Recipient number, in whatever raw format was stored
     *                      (e.g. a customer's phone field) — the driver is
     *                      responsible for normalizing it to what its gateway expects.
     * @param  string  $message  Plain text message body.
     * @param  array{salon_id?: int|null}  $context  Metadata a driver may use without
     *                      it being part of the gateway call itself — e.g. LogSmsDriver
     *                      records salon_id; a real gateway driver can ignore it.
     *
     * @throws SmsSendException on any failure (auth, validation, transport, gateway-reported error).
     */
    public function send(string $to, string $message, array $context = []): void;
}
