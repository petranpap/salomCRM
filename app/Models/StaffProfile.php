<?php

namespace App\Models;

use App\Support\WeeklySchedule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'working_hours',
        'color',
        'specialty',
        'job_title',
        'is_active',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'is_active'     => 'boolean',
    ];

    // No direct salon_id column — scoped through the owning user instead.
    protected static function booted(): void
    {
        static::addGlobalScope('salon', function (Builder $builder) {
            if (! auth()->check()) {
                return;
            }

            $user = auth()->user();

            if ($user->isSuperAdmin()) {
                return;
            }

            $builder->whereHas('user', fn ($q) => $q->where('salon_id', $user->salon_id));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Always returns all 7 days, even if some are missing from the stored value
     * (e.g. a profile that has never had its schedule set).
     */
    public function normalizedWorkingHours(): array
    {
        return WeeklySchedule::normalize($this->working_hours);
    }
}
