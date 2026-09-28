<?php

namespace App\Support;

use App\Models\Appointment;

/**
 * A minimal RFC 5545 .ics file for one appointment, so "Add to Calendar" works from
 * the reminder email in Apple Calendar, Google Calendar, Outlook, etc. — they all
 * recognize a text/calendar attachment without needing a click-through link.
 */
class AppointmentIcs
{
    public static function build(Appointment $appointment): string
    {
        $salon = $appointment->salon;
        $salonName = $salon?->name ?? config('app.name');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//' . config('app.name') . '//Appointment Reminder//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:appointment-' . $appointment->id . '@' . (parse_url(config('app.url'), PHP_URL_HOST) ?: 'studiokassandra.com'),
            'DTSTAMP:' . now()->format('Ymd\THis\Z'),
            'DTSTART:' . $appointment->start->format('Ymd\THis\Z'),
            'DTEND:' . $appointment->end->format('Ymd\THis\Z'),
            'SUMMARY:' . self::escape(($appointment->service?->name ?? 'Appointment') . ' — ' . $salonName),
            'DESCRIPTION:' . self::escape('With ' . ($appointment->staffProfile?->user?->name ?? 'our team') . ' at ' . $salonName),
            'LOCATION:' . self::escape($salon?->address ?? ''),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines) . "\r\n";
    }

    private static function escape(string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\n"],
            ['\\\\', '\\;', '\\,', '\\n'],
            $value
        );
    }
}
