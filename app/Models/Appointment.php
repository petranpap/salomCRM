<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSalon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'customer_id',
        'service_id',
        'staff_id',
        'start',
        'end',
        'status',
        'was_late',
        'notes',
    ];

    protected $casts = [
        'start'    => 'datetime',
        'end'      => 'datetime',
        'was_late' => 'boolean',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // staff_id FK points to staff_profiles.id
    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_id');
    }

    // Convenience: get the User behind the staff profile
    public function staffUser()
    {
        return $this->hasOneThrough(User::class, StaffProfile::class, 'id', 'id', 'staff_id', 'user_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function treatment()
    {
        return $this->hasOne(Treatment::class);
    }

    public function reminders()
    {
        return $this->hasMany(AppointmentReminder::class);
    }

    protected static function booted(): void
    {
        static::updated(function (Appointment $appointment) {
            if ($appointment->wasChanged('status') && $appointment->status === 'completed') {
                $appointment->recordTreatment();
            }

            if ($appointment->wasChanged('status') && $appointment->status === 'no-show') {
                $appointment->autoFlagCustomer('no_show', 'Auto-flagged after a no-show on ' . $appointment->start->format('d/m/Y') . '.');
            }

            if ($appointment->wasChanged('was_late') && $appointment->was_late) {
                $appointment->autoFlagCustomer('late', 'Auto-flagged after arriving late on ' . $appointment->start->format('d/m/Y') . '.');
            }
        });
    }

    /**
     * Sets a customer flag the first time this kind of behavior is seen — never overwrites
     * a flag the owner already set (manually or from an earlier auto-flag), since that's
     * treated as a deliberate decision.
     */
    public function autoFlagCustomer(string $flagType, string $note): void
    {
        $customer = $this->customer;

        if (! $customer || $customer->flag_type) {
            return;
        }

        $customer->update(['flag_type' => $flagType, 'flag_notes' => $note]);
    }

    /**
     * Snapshot a completed appointment into treatment history — what was done, by whom,
     * for how much, and which products were used, sourced from its paid payment when one
     * exists so the price/products reflect what the customer actually paid for.
     */
    public function recordTreatment(): void
    {
        if (! $this->customer_id || ! $this->service_id) {
            return;
        }

        $payment = $this->payments()->where('status', 'paid')->latest('paid_at')->first();
        $price = $payment ? $payment->amount - $payment->products_total : $this->service?->base_price;

        $treatment = Treatment::updateOrCreate(
            ['appointment_id' => $this->id],
            [
                'customer_id' => $this->customer_id,
                'service_id'  => $this->service_id,
                'staff_id'    => $this->staff_id,
                'date'        => $this->start->toDateString(),
                'price'       => $price ?? 0,
                'notes'       => $this->notes,
            ]
        );

        if ($payment && $payment->products->isNotEmpty()) {
            $treatment->products()->sync(
                $payment->products->mapWithKeys(fn ($product) => [
                    $product->id => ['qty_used' => $product->pivot->quantity],
                ])
            );
        }
    }
}
