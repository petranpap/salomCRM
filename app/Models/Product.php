<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSalon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'sku',
        'name',
        'category',
        'brand',
        'cost_price',
        'sell_price',
        'stock_qty',
        'threshold_qty',
        'status',
        'image_url',
    ];

    protected $casts = [
        'stock_qty'     => 'integer',
        'threshold_qty' => 'integer',
        'cost_price'    => 'decimal:2',
        'sell_price'    => 'decimal:2',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function movements()
    {
        return $this->hasMany(ProductMovement::class);
    }

    public function treatments()
    {
        return $this->belongsToMany(Treatment::class, 'treatment_products')
            ->withPivot('qty_used')
            ->withTimestamps();
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->stock_qty <= $this->threshold_qty;
    }
}
