const DAY_LABELS = [
    ['monday', 'Monday'],
    ['tuesday', 'Tuesday'],
    ['wednesday', 'Wednesday'],
    ['thursday', 'Thursday'],
    ['friday', 'Friday'],
    ['saturday', 'Saturday'],
    ['sunday', 'Sunday'],
];

function formatWindows(entry) {
    if (!entry || !entry.active) return 'Closed';

    const windows = [];
    if (entry.start && entry.end) windows.push(`${entry.start}–${entry.end}`);
    if (entry.start2 && entry.end2) windows.push(`${entry.start2}–${entry.end2}`);

    return windows.length ? windows.join(', ') : 'Closed';
}

function weeklySchedule(initial, limits) {
    const days = DAY_LABELS.map(([key, label]) => ({ key, label }));

    const hours = {};
    days.forEach((d) => {
        const existing = (initial || {})[d.key] || {};
        hours[d.key] = {
            active: !!existing.active,
            start: existing.start || '09:00',
            end: existing.end || '17:00',
            // A break (midday closure, e.g. a lunch break) is optional — present only
            // once both its start and end are filled in.
            hasBreak: !!(existing.start2 && existing.end2),
            start2: existing.start2 || '13:00',
            end2: existing.end2 || '14:00',
        };
    });

    return {
        days,
        hours,
        limits: limits || null,
        limitLabel(dayKey) {
            return formatWindows(this.limits?.[dayKey]);
        },
        toggleBreak(dayKey) {
            this.hours[dayKey].hasBreak = !this.hours[dayKey].hasBreak;
        },
    };
}

window.weeklySchedule = weeklySchedule;
