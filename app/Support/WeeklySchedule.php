<?php

namespace App\Support;

/**
 * Shared shape for anything with a Mon–Sun open/closed + hours schedule (staff working
 * hours, salon opening hours): every day present, ['active' => bool, 'start' => ?string,
 * 'end' => ?string, 'start2' => ?string, 'end2' => ?string]. The second window is optional
 * — most days only use start/end — and exists to support a midday closure ("split shift"),
 * e.g. 09:00–13:30 and 16:00–18:30. Anything checking whether a time falls within a day's
 * hours should go through windows()/formatDay() rather than reading start/end directly, so
 * it doesn't silently ignore the second window.
 */
class WeeklySchedule
{
    const DAYS = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    public static function normalize(?array $stored): array
    {
        $stored = $stored ?? [];
        $result = [];

        foreach (self::DAYS as $day) {
            $result[$day] = [
                'active' => (bool) ($stored[$day]['active'] ?? false),
                'start'  => $stored[$day]['start'] ?? null,
                'end'    => $stored[$day]['end'] ?? null,
                'start2' => $stored[$day]['start2'] ?? null,
                'end2'   => $stored[$day]['end2'] ?? null,
            ];
        }

        return $result;
    }

    /**
     * Collapse a submitted form's {day}[active|start|end|start2|end2] fields into the
     * shape above. The second window only counts if both its start and end were filled in.
     */
    public static function fromRequest(array $input): array
    {
        $result = [];

        foreach (self::DAYS as $day) {
            $active = !empty($input[$day]['active']);
            $start2 = $active ? ($input[$day]['start2'] ?? null) : null;
            $end2   = $active ? ($input[$day]['end2'] ?? null) : null;

            $result[$day] = [
                'active' => $active,
                'start'  => $active ? ($input[$day]['start'] ?? null) : null,
                'end'    => $active ? ($input[$day]['end'] ?? null) : null,
                'start2' => ($start2 && $end2) ? $start2 : null,
                'end2'   => ($start2 && $end2) ? $end2 : null,
            ];
        }

        return $result;
    }

    /**
     * The list of time windows a normalized day entry actually has — one, two, or (if
     * inactive/unset) none. Every consumer that checks "is this time within the day's
     * hours" should iterate this rather than read start/end directly, so a split shift
     * is never silently collapsed to just its first window.
     */
    public static function windows(array $entry): array
    {
        if (! ($entry['active'] ?? false)) {
            return [];
        }

        $windows = [];

        if (! empty($entry['start']) && ! empty($entry['end'])) {
            $windows[] = ['start' => $entry['start'], 'end' => $entry['end']];
        }

        if (! empty($entry['start2']) && ! empty($entry['end2'])) {
            $windows[] = ['start' => $entry['start2'], 'end' => $entry['end2']];
        }

        return $windows;
    }

    /**
     * Human-readable summary of a day entry, e.g. "09:00–13:30, 16:00–18:30" or "Closed".
     */
    public static function formatDay(array $entry): string
    {
        $windows = self::windows($entry);

        if (empty($windows)) {
            return 'Closed';
        }

        return collect($windows)->map(fn ($w) => "{$w['start']}–{$w['end']}")->join(', ');
    }

    /**
     * Checks a normalized schedule (e.g. a staff member's working hours) falls within a
     * second normalized schedule (e.g. the salon's opening hours) on every active day —
     * each of the subject's windows must fit entirely inside at least one of the limit's
     * windows. Returns one human-readable message per violation; empty array means fine.
     */
    public static function validateWithin(array $schedule, array $limits, string $subjectLabel): array
    {
        $errors = [];

        foreach (self::DAYS as $day) {
            $entry = $schedule[$day] ?? null;
            $subjectWindows = self::windows($entry ?? []);

            if (empty($subjectWindows)) {
                continue;
            }

            $limit = $limits[$day] ?? null;
            $dayLabel = ucfirst($day) . 's';
            $limitWindows = self::windows($limit ?? []);

            if (empty($limitWindows)) {
                $errors[] = "{$subjectLabel} can't work {$dayLabel} — the salon is closed that day.";
                continue;
            }

            foreach ($subjectWindows as $subjectWindow) {
                $fitsSomewhere = collect($limitWindows)->contains(
                    fn ($limitWindow) => $subjectWindow['start'] >= $limitWindow['start'] && $subjectWindow['end'] <= $limitWindow['end']
                );

                if (! $fitsSomewhere) {
                    $limitLabel = self::formatDay($limit);
                    $errors[] = "{$subjectLabel}'s {$dayLabel} hours ({$subjectWindow['start']}–{$subjectWindow['end']}) must fall within the salon's opening hours on {$dayLabel} ({$limitLabel}).";
                    break;
                }
            }
        }

        return $errors;
    }

    /**
     * FullCalendar's businessHours option uses 0 (Sunday) – 6 (Saturday). One entry per
     * window, so a split day naturally shows as two shaded ranges with a gap between.
     */
    public static function toFullCalendarBusinessHours(array $normalized): array
    {
        $dowIndex = ['sunday' => 0, 'monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6];

        $entries = [];

        foreach ($normalized as $day => $hours) {
            foreach (self::windows($hours) as $window) {
                $entries[] = [
                    'daysOfWeek' => [$dowIndex[$day]],
                    'startTime'  => $window['start'],
                    'endTime'    => $window['end'],
                ];
            }
        }

        return $entries;
    }
}
