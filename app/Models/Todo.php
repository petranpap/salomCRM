<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'status',
        'due_date',
        'assigned_to',
        'payload',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'payload'  => 'json',
    ];

    // No direct salon_id column — scoped through the assignee. Unassigned todos stay
    // visible to everyone (matches this app's existing "shared task" convention).
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

            $builder->where(function ($q) use ($user) {
                $q->whereNull('assigned_to')
                    ->orWhereHas('assignee', fn ($u) => $u->where('salon_id', $user->salon_id));
            });
        });
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}