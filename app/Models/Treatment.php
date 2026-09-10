<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treatment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_id',
        'customer_id',
        'service_id',
        'staff_id',
        'date',
        'price',
        'products_used',
        'notes',
    ];

    protected $casts = [
        'products_used' => 'array',
        'date'          => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsTo(StaffProfile::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'treatment_products')
            ->withPivot('qty_used')
            ->withTimestamps();
    }
}
