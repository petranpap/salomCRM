<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Salon;
use App\Models\Service;
use App\Models\StaffProfile;
use App\Models\ZReportClosure;
use App\Support\WeeklySchedule;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isCreate = $this->isMethod('POST');

        // A create must supply every field; an update may send just the one thing
        // that's changing (e.g. the API updating only `status`) — "sometimes" skips
        // a rule entirely when the field isn't present, rather than requiring it.
        $required = $isCreate ? 'required' : 'sometimes|required';

        $startRule = "{$required}|date";
        if ($isCreate) {
            $startRule .= '|after_or_equal:now';
        }

        $statusRule = $isCreate
            ? null
            : 'sometimes|required|in:booked,confirmed,completed,no-show,canceled';

        $rules = [
            'customer_id' => "{$required}|exists:customers,id",
            'service_id'  => "{$required}|exists:services,id",
            'staff_id'    => "{$required}|exists:staff_profiles,id",
            'start'       => $startRule,
            'end'         => "{$required}|date|after:start",
            'was_late'    => 'sometimes|boolean',
            'notes'       => 'nullable|string|max:255',
        ];

        if ($statusRule !== null) {
            $rules['status'] = $statusRule;
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'customer_id.required'  => 'A customer is required.',
            'service_id.required'   => 'A service is required.',
            'staff_id.required'     => 'A staff member is required.',
            'start.required'        => 'The start time is required.',
            'start.after_or_equal'  => 'The start time must be a future date.',
            'end.required'          => 'The end time is required.',
            'end.after'             => 'The end time must be after the start time.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $existingAppointment = $this->route('appointment');

            if ($existingAppointment && ZReportClosure::isLocked($existingAppointment->salon_id, $existingAppointment->start)) {
                $validator->errors()->add('start', 'This appointment falls on a day that has already been closed and can no longer be edited.');

                return;
            }

            $salon = $this->resolveSalon();

            // Cross-tenant guard: the `exists:` rules above only confirm these IDs exist
            // *somewhere*, not that they belong to this appointment's salon. Query without
            // the global salon scope and compare explicitly — relying on the scope alone
            // wouldn't catch a super_admin being handed a mismatched salon_id/customer_id
            // pair, since super_admin queries bypass that scope entirely.
            $staffProfile = null;

            if ($salon) {
                if ($this->filled('customer_id')) {
                    $customer = Customer::withoutGlobalScopes()->find($this->input('customer_id'));
                    if (! $customer || $customer->salon_id !== $salon->id) {
                        $validator->errors()->add('customer_id', 'The selected customer is invalid.');
                    }
                }

                if ($this->filled('service_id')) {
                    $service = Service::withoutGlobalScopes()->find($this->input('service_id'));
                    if (! $service || $service->salon_id !== $salon->id) {
                        $validator->errors()->add('service_id', 'The selected service is invalid.');
                    }
                }
            }

            if ($this->filled('staff_id')) {
                $staffProfile = StaffProfile::withoutGlobalScopes()->with('user')->find($this->input('staff_id'));

                if (! $staffProfile || ($salon && $staffProfile->user?->salon_id !== $salon->id)) {
                    $validator->errors()->add('staff_id', 'The selected staff member is invalid.');
                    $staffProfile = null;
                }
            }

            if (! $this->filled('start') || ! $this->filled('end')) {
                return;
            }

            try {
                $start = Carbon::parse($this->input('start'));
                $end   = Carbon::parse($this->input('end'));
            } catch (\Exception) {
                return;
            }

            $day = strtolower($start->format('l'));
            $dayLabel = ucfirst($day) . 's';

            if ($salon) {
                $salonHours = $salon->normalizedOpeningHours()[$day];

                if (! $salonHours['active']) {
                    $validator->errors()->add('start', "The salon is closed on {$dayLabel}.");
                } elseif (! $this->withinWindow($start, $end, $salonHours)) {
                    $validator->errors()->add('start', "The salon is only open " . WeeklySchedule::formatDay($salonHours) . " on {$dayLabel}.");
                }
            }

            if ($staffProfile) {
                $staffHours = $staffProfile->normalizedWorkingHours()[$day];
                $staffName = $staffProfile->user?->name ?? 'This staff member';

                if (! $staffHours['active']) {
                    $validator->errors()->add('staff_id', "{$staffName} does not work on {$dayLabel}.");
                } elseif (! $this->withinWindow($start, $end, $staffHours)) {
                    $validator->errors()->add('staff_id', "{$staffName} works " . WeeklySchedule::formatDay($staffHours) . " on {$dayLabel}.");
                }
            }

            if ($this->filled('customer_id')) {
                $overlapping = Appointment::where('customer_id', $this->input('customer_id'))
                    ->where('status', '!=', 'canceled')
                    ->when($existingAppointment, fn($q) => $q->where('id', '!=', $existingAppointment->id))
                    ->where('start', '<', $end)
                    ->where('end', '>', $start)
                    ->exists();

                if ($overlapping) {
                    $validator->errors()->add('start', 'This customer already has another appointment that overlaps with this time.');
                }
            }
        });
    }

    /**
     * True if [$start, $end] fits entirely inside *any one* of the day's windows — an
     * appointment can't span a midday closure, even if the salon is open before and after it.
     */
    protected function withinWindow(Carbon $start, Carbon $end, array $hours): bool
    {
        foreach (WeeklySchedule::windows($hours) as $window) {
            $windowStart = Carbon::parse($start->format('Y-m-d') . ' ' . $window['start']);
            $windowEnd   = Carbon::parse($start->format('Y-m-d') . ' ' . $window['end']);

            if ($start->gte($windowStart) && $end->lte($windowEnd)) {
                return true;
            }
        }

        return false;
    }

    protected function resolveSalon(): ?Salon
    {
        $user = $this->user();

        if (! $user->isSuperAdmin()) {
            return $user->salon;
        }

        if ($this->filled('salon_id')) {
            return Salon::find($this->input('salon_id'));
        }

        $customer = Customer::find($this->input('customer_id'));

        return $customer?->salon;
    }
}
