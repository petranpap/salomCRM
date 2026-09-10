<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSalon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, BelongsToSalon, Notifiable;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'notes',
        'consent_email',
        'consent_sms',
        'date_of_birth',
        'salon_id',
        'flag_type',
        'flag_notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'consent_email' => 'boolean',
        'consent_sms'   => 'boolean',
    ];

    public function isFlagged(): bool
    {
        return $this->flag_type !== null && $this->flag_type !== 'vip';
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    /**
     * Where an SMS reminder should be sent — the SMS channel/driver is responsible
     * for normalizing this into whatever format the underlying gateway expects.
     */
    public function routeNotificationForSms(): ?string
    {
        return $this->phone;
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
}
