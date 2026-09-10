<?php

namespace App\Services\Sms\Drivers;

use App\Services\Sms\SmsDriver;
use App\Services\Sms\SmsSendException;
use Illuminate\Support\Facades\Http;

/**
 * Cyta's "Web SMS API" (WebSmsApiGuide v3, cyta.com.cy). Cyprus mobile numbers only
 * (format 9xxxxxxx — no country code), XML request/response, auth via a per-account
 * username + secret key (no request signing).
 */
class CytaSmsDriver implements SmsDriver
{
    protected const ENDPOINT = 'https://www.cyta.com.cy/cytamobilevodafone/dev/websmsapi/sendsms.aspx';

    protected const MAX_MESSAGE_LENGTH = 612;

    protected const STATUS_MESSAGES = [
        1  => 'Not allowed to use the service.',
        2  => 'The service is suspended.',
        9  => 'Generic send SMS failure.',
        10 => 'User not found, account suspended, or invalid secret key.',
        11 => 'Configuration settings not found for this username.',
        12 => 'Web SMS API suspended, or terms not accepted.',
        13 => "Client IP doesn't match the account's expected IP.",
        19 => 'Registered mobile number for this username not found.',
        20 => 'Missing field values, or wrong case used on XML elements.',
        21 => 'Invalid username.',
        22 => 'Invalid characters in recipients.',
        23 => 'Invalid characters in recipient count.',
        24 => 'Invalid language.',
        25 => "Recipient count doesn't match the number of mobiles sent.",
        26 => 'Recipients list is bigger than allowed.',
        27 => 'Invalid mobile number found.',
        28 => 'Message length is bigger than allowed.',
        29 => 'Unsupported content type.',
        30 => 'Missing HTTP POST request body.',
        31 => 'Max allowed SMS messages per day threshold reached.',
        39 => 'Invalid version.',
        90 => 'Exception.',
        91 => 'Exception processing URL-encoded request.',
        92 => 'Exception processing XML request.',
        93 => 'Invalid XML request data.',
    ];

    public function __construct(
        protected ?string $username,
        protected ?string $secretKey,
        protected string $language = 'en',
    ) {
    }

    public function send(string $to, string $message, array $context = []): void
    {
        if (! $this->username || ! $this->secretKey) {
            throw new SmsSendException('Cyta SMS is not configured (CYTA_SMS_USERNAME / CYTA_SMS_SECRET_KEY missing).');
        }

        if (mb_strlen($message) > self::MAX_MESSAGE_LENGTH) {
            throw new SmsSendException('Message exceeds Cyta\'s maximum length of ' . self::MAX_MESSAGE_LENGTH . ' characters.');
        }

        $mobile = $this->normalizeMobile($to);
        $body   = $this->buildRequestXml($mobile, $message);

        $response = Http::withBody($body, 'application/xml; charset="utf-8"')
            ->post(self::ENDPOINT);

        if (! $response->successful()) {
            throw new SmsSendException("Cyta SMS request failed with HTTP {$response->status()}.");
        }

        $this->assertSuccessStatus($response->body());
    }

    /**
     * Cyta only accepts Cyprus mobiles in the format 9xxxxxxx (8 digits, no country
     * code) — strips spaces/dashes and a +357 / 00357 / 357 prefix a customer's
     * stored phone number might carry.
     */
    protected function normalizeMobile(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw);

        foreach (['00357', '357'] as $prefix) {
            if (str_starts_with($digits, $prefix)) {
                $digits = substr($digits, strlen($prefix));
                break;
            }
        }

        if (! preg_match('/^9\d{7}$/', $digits)) {
            throw new SmsSendException("'{$raw}' is not a valid Cyprus mobile number for Cyta SMS (expected 9xxxxxxx).");
        }

        return $digits;
    }

    protected function buildRequestXml(string $mobile, string $message): string
    {
        $escape = fn (string $value) => htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return '<?xml version="1.0" encoding="UTF-8" ?>'
            . '<websmsapi>'
            . '<version>1.0</version>'
            . '<username>' . $escape($this->username) . '</username>'
            . '<secretkey>' . $escape($this->secretKey) . '</secretkey>'
            . '<recipients>'
            . '<count>1</count>'
            . '<mobiles><m>' . $mobile . '</m></mobiles>'
            . '</recipients>'
            . '<message>' . $escape($message) . '</message>'
            . '<language>' . $escape($this->language) . '</language>'
            . '</websmsapi>';
    }

    /**
     * A successful HTTP response can still carry a logical failure — the real result
     * is <status> in the XML body (0 = sent; anything else maps to STATUS_MESSAGES).
     */
    protected function assertSuccessStatus(string $responseBody): void
    {
        $previous = libxml_use_internal_errors(true);
        $xml = simplexml_load_string($responseBody);
        libxml_use_internal_errors($previous);

        if ($xml === false) {
            throw new SmsSendException('Cyta SMS returned an unparseable response: ' . $responseBody);
        }

        $status = (int) $xml->status;

        if ($status !== 0) {
            throw new SmsSendException(
                "Cyta SMS error {$status}: " . (self::STATUS_MESSAGES[$status] ?? 'Unknown error.')
            );
        }
    }
}
