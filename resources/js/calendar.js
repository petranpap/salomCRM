import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import enGbLocale from '@fullcalendar/core/locales/en-gb';

function toLocalDatetimeValue(date) {
    const pad = (n) => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

/**
 * businessHours entries look like { daysOfWeek: [1], startTime: '09:00', endTime: '18:00' }.
 * Returns true when businessHours is empty/unset (i.e. no restriction configured).
 */
function isWithinBusinessHours(date, businessHours) {
    if (!businessHours || !businessHours.length) return true;

    const day = date.getDay();
    const minutesOfDay = date.getHours() * 60 + date.getMinutes();

    return businessHours.some((bh) => {
        if (!bh.daysOfWeek.includes(day)) return false;
        const [sh, sm] = bh.startTime.split(':').map(Number);
        const [eh, em] = bh.endTime.split(':').map(Number);
        return minutesOfDay >= sh * 60 + sm && minutesOfDay < eh * 60 + em;
    });
}

const TIME_FORMAT_24H = { hour: '2-digit', minute: '2-digit', hour12: false };

/**
 * Renders a FullCalendar instance and wires up click-to-create.
 * @param {string} elementId  DOM id of the container element.
 * @param {Array}  events     FullCalendar event objects.
 * @param {Object} options    Overrides merged into the base calendar config.
 *                            options.businessHours (optional) also constrains dateClick.
 */
function initSalonCalendar(elementId, events, options = {}) {
    const el = document.getElementById(elementId);
    if (!el) return null;

    const businessHours = options.businessHours || null;

    const calendar = new Calendar(el, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        locale: enGbLocale,
        firstDay: 1,
        initialView: window.innerWidth < 768 ? 'timeGridDay' : 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay',
        },
        views: {
            timeGridWeek: { dayHeaderFormat: { weekday: 'short', day: '2-digit', month: '2-digit' } },
            timeGridDay:  { dayHeaderFormat: { weekday: 'short', day: '2-digit', month: '2-digit' } },
        },
        slotLabelFormat: TIME_FORMAT_24H,
        eventTimeFormat: TIME_FORMAT_24H,
        height: 'auto',
        slotMinTime: '08:00:00',
        slotMaxTime: '21:00:00',
        allDaySlot: false,
        nowIndicator: true,
        businessHours: businessHours || undefined,
        events,
        eventClick(info) {
            window.location.href = '/appointments/' + info.event.id;
        },
        dateClick(info) {
            if (!isWithinBusinessHours(info.date, businessHours)) {
                alert('The salon is closed at this time — pick a time within opening hours.');
                return;
            }
            window.location.href = '/appointments/create?start=' + encodeURIComponent(toLocalDatetimeValue(info.date));
        },
        ...options,
    });

    calendar.render();
    return calendar;
}

window.SalonCalendar = { init: initSalonCalendar };
