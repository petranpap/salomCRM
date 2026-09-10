import './bootstrap';
import './calendar';
import './weekly-schedule';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';

document.addEventListener('DOMContentLoaded', () => {

    // ── Date-only inputs ──────────────────────────────────────────────
    document.querySelectorAll('input[type="date"]').forEach(el => {
        flatpickr(el, {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'D, d M Y',
            allowInput: false,
            disableMobile: true,
        });
    });

    // ── Time-only inputs (weekly schedule editors) — always 24h, regardless
    //    of OS/browser locale, which native <input type="time"> can't guarantee ──
    document.querySelectorAll('.js-time-input').forEach(el => {
        flatpickr(el, {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',
            time_24hr: true,
            minuteIncrement: 5,
            allowInput: false,
            disableMobile: true,
        });
    });

    // ── Datetime inputs ───────────────────────────────────────────────
    const startEl   = document.getElementById('appt-start');
    const endEl     = document.getElementById('appt-end');
    const serviceEl = document.getElementById('appt-service');

    // Generic datetime init for any datetime-local that is NOT appt-start/end
    document.querySelectorAll('input[type="datetime-local"]').forEach(el => {
        if (el === startEl || el === endEl) return; // handled below
        flatpickr(el, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            altInput: true,
            altFormat: 'D, d M Y · H:i',
            time_24hr: true,
            minuteIncrement: 5,
            allowInput: false,
            disableMobile: true,
        });
    });

    // ── Appointment start/end with auto-end logic ─────────────────────
    if (startEl && endEl) {
        function getDuration() {
            if (!serviceEl) return null;
            const opt = serviceEl.options[serviceEl.selectedIndex];
            const d = parseInt(opt?.dataset?.duration);
            return isNaN(d) ? null : d;
        }

        function calcEnd(startDate) {
            const duration = getDuration();
            if (!startDate || !duration) return;
            const endDate = new Date(startDate.getTime() + duration * 60000);
            endFp.setDate(endDate, true);
        }

        const startFp = flatpickr(startEl, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            altInput: true,
            altFormat: 'D, d M Y · H:i',
            time_24hr: true,
            minuteIncrement: 5,
            allowInput: false,
            disableMobile: true,
            onChange(dates) { calcEnd(dates[0]); },
            // Belt-and-suspenders: also fire once the picker is dismissed (mouse click-away
            // or touch tap-away), so the end time is guaranteed filled once the user is done
            // with the start field, not just mid-selection.
            onClose(dates) { calcEnd(dates[0]); },
        });

        const endFp = flatpickr(endEl, {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            altInput: true,
            altFormat: 'D, d M Y · H:i',
            time_24hr: true,
            minuteIncrement: 5,
            allowInput: false,
            disableMobile: true,
        });

        // Re-calculate if user changes service after start is already set
        serviceEl?.addEventListener('change', () => {
            const dates = startFp.selectedDates;
            if (dates.length) calcEnd(dates[0]);
        });
    }

});
