<?php

namespace App\Http\Controllers;

use App\Models\AppointmentReminder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderLogController extends Controller
{
    /**
     * Every reminder send attempt (email or SMS), who it was for, and whether it
     * actually went out — the audit trail behind "why didn't my customer get a
     * reminder?". Scoped to the current salon via the appointment relation's own
     * BelongsToSalon global scope; super_admin sees every salon's attempts.
     */
    public function index(Request $request): View
    {
        $reminders = AppointmentReminder::query()
            ->whereHas('appointment')
            ->with(['appointment.customer', 'appointment.service', 'appointment.salon', 'appointment.staffProfile.user'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('channel'), fn ($query) => $query->where('channel', $request->input('channel')))
            ->latest('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('reminders.index', ['reminders' => $reminders]);
    }
}
