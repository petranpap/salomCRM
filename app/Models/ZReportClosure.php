<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSalon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ZReportClosure extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'sequence_number',
        'report_date',
        'total_revenue',
        'cash_revenue',
        'card_revenue',
        'retail_revenue',
        'vat_collected',
        'refunds_total',
        'receipt_count',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'report_date' => 'date',
        'closed_at'   => 'datetime',
    ];

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /**
     * Whether the given salon already has a permanent Z-report for that date —
     * once true, payments/appointments on that date must no longer be mutated.
     */
    public static function isLocked(?int $salonId, Carbon|string $date): bool
    {
        if (! $salonId) {
            return false;
        }

        $date = $date instanceof Carbon ? $date->toDateString() : Carbon::parse($date)->toDateString();

        return static::withoutGlobalScopes()
            ->where('salon_id', $salonId)
            ->where('report_date', $date)
            ->exists();
    }
}
