<?php

namespace App\Services;

use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Illuminate\Support\Facades\Notification;

class ReminderService
{
    public function sendReminders()
    {
        $appointments = Appointment::where('start', '>=', now())
            ->where('start', '<=', now()->addDays(1))
            ->where('status', 'booked')
            ->get();

        foreach ($appointments as $appointment) {
            Notification::send($appointment->customer, new AppointmentReminder($appointment));
        }
    }
}