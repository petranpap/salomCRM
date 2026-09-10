<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSalon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'appointment_id',
        'customer_id',
        'guest_name',
        'staff_id',
        'amount',
        'method',
        'status',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'payment_products')
            ->withPivot('quantity', 'unit_price')
            ->withTimestamps();
    }

    public function getProductsTotalAttribute(): float
    {
        return $this->products->sum(fn ($product) => $product->pivot->quantity * $product->pivot->unit_price);
    }

    public function getDisplayCustomerNameAttribute(): string
    {
        return $this->customer?->name
            ?? $this->appointment?->customer?->name
            ?? $this->guest_name
            ?? 'Walk-in customer';
    }

    /**
     * VAT breakdown, treating `amount` as VAT-inclusive (the total the customer paid) —
     * the standard convention for salon service/retail pricing shown to customers.
     */
    public function getVatAmountAttribute(): ?float
    {
        if (! $this->salon?->hasVat()) {
            return null;
        }

        return round($this->amount - $this->net_amount, 2);
    }

    public function getNetAmountAttribute(): float
    {
        if (! $this->salon?->hasVat()) {
            return (float) $this->amount;
        }

        return round($this->amount / (1 + $this->salon->vat_rate / 100), 2);
    }
}
