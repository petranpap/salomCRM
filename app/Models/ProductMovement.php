<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'qty_change',
        'reason',
        'ref_type',
        'ref_id',
        'user_id',
    ];

    protected $casts = [
        'qty_change' => 'integer',
        'user_id' => 'integer',
        'ref_id' => 'integer',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}