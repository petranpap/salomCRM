<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\AppointmentReminder as AppointmentReminderLog;
use App\Notifications\AppointmentReminder as AppointmentReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:send-reminders';
    protected $description = 'Send appointment reminders (email/SMS) to consented customers for appointments starting within the configured window.';

    public function handle(): int
    {
        $cutoff = now()->addHours((int) config('reminders.hours_before', 24));

        $appointments = Appointment::query()
            ->whereIn('status', ['booked', 'confirmed'])
            ->whereBetween('start', [now(), $cutoff])
            ->with(['customer', 'service', 'staffProfile.user', 'salon'])
            ->get();

        $sent = 0;

        foreach ($appointments as $appointment) {
            $customer = $appointment->customer;

            if (! $customer) {
                continue;
            }

            foreach ($this->consentedChannels($customer) as $channel) {
                $alreadySent = AppointmentReminderLog::where('appointment_id', $appointment->id)
                    ->where('channel', $channel)
                    ->where('status', 'sent')
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                try {
                    $customer->notify(new AppointmentReminderNotification($appointment, $channel));

                    AppointmentReminderLog::create([
                        'appointment_id' => $appointment->id,
                        'channel'        => $channel,
                        'status'         => 'sent',
                        'sent_at'        => now(),
                    ]);

                    $sent++;
                } catch (Throwable $e) {
                    AppointmentReminderLog::create([
                        'appointment_id' => $appointment->id,
                        'channel'        => $channel,
                        'status'         => 'failed',
                        'error'          => $e->getMessage(),
                    ]);

                    Log::error("Reminder failed for appointment {$appointment->id} via {$channel}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Reminders processed: {$sent} sent.");

        return self::SUCCESS;
    }

    protected function consentedChannels($customer): array
    {
        $channels = [];

        if ($customer->consent_email && $customer->email) {
            $channels[] = 'mail';
        }

        if ($customer->consent_sms && $customer->phone) {
            $channels[] = 'sms';
        }

        return $channels;
    }
}
