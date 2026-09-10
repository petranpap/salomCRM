<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSalon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingChange extends Model
{
    use HasFactory, BelongsToSalon;

    protected $fillable = [
        'salon_id',
        'subject_type',
        'subject_id',
        'action',
        'payload',
        'submitted_by',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_notes',
    ];

    protected $casts = [
        'payload'     => 'array',
        'reviewed_at' => 'datetime',
    ];

    const FIELD_LABELS = [
        'name'           => 'Name',
        'category_id'    => 'Category',
        'duration_min'   => 'Duration (min)',
        'base_price'     => 'Base Price',
        'is_active'      => 'Active',
        'sku'            => 'SKU',
        'category'       => 'Category',
        'brand'          => 'Brand',
        'cost_price'     => 'Cost Price',
        'sell_price'     => 'Sell Price',
        'stock_qty'      => 'Stock Qty',
        'threshold_qty'  => 'Low Stock Threshold',
        'status'         => 'Status',
        'working_hours'  => 'Working Hours',
        'color'          => 'Color',
        'description'    => 'Description',
        'sort_order'     => 'Sort Order',
    ];

    const SUBJECT_LABELS = [
        Service::class         => 'Service',
        Product::class         => 'Product',
        ServiceCategory::class => 'Category',
        StaffProfile::class    => 'Staff Working Hours',
    ];

    public function subject()
    {
        return $this->morphTo();
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function subjectLabel(): string
    {
        return self::SUBJECT_LABELS[$this->subject_type] ?? class_basename($this->subject_type);
    }

    public function title(): string
    {
        $label = $this->subjectLabel();

        if ($this->action === 'create') {
            return "New {$label}: " . ($this->payload['name'] ?? 'Untitled');
        }

        $subject = $this->subject;
        $name = $subject?->name
            ?? $subject?->user?->name
            ?? null;

        $verb = $this->action === 'delete' ? 'Delete' : 'Edit';

        return "{$verb} {$label}" . ($name ? ": {$name}" : '');
    }

    /**
     * For updates: one row per changed field, with the subject's current value alongside
     * the proposed one. For creates: the proposed values with no "from". Deletes have
     * nothing to diff — the view renders a plain "will be deleted" notice instead.
     */
    public function changes(): array
    {
        if ($this->action === 'delete') {
            return [];
        }

        if ($this->subject_type === StaffProfile::class && array_key_exists('working_hours', $this->payload)) {
            return $this->workingHoursChanges();
        }

        $subject = $this->action === 'update' ? $this->subject : null;
        $rows = [];

        foreach ($this->payload as $field => $value) {
            if (in_array($field, ['working_hours', 'salon_id'], true)) {
                continue;
            }

            $rows[] = [
                'label' => self::FIELD_LABELS[$field] ?? ucfirst(str_replace('_', ' ', $field)),
                'from'  => $subject?->{$field},
                'to'    => $value,
            ];
        }

        return $rows;
    }

    protected function workingHoursChanges(): array
    {
        $current = $this->subject?->normalizedWorkingHours() ?? [];
        $proposed = $this->payload['working_hours'] ?? [];
        $rows = [];

        $blank = ['active' => false, 'start' => null, 'end' => null, 'start2' => null, 'end2' => null];

        foreach (\App\Support\WeeklySchedule::DAYS as $day) {
            $from = $current[$day] ?? $blank;
            $to = $proposed[$day] ?? $blank;

            if ($from == $to) {
                continue;
            }

            $rows[] = [
                'label' => ucfirst($day),
                'from'  => \App\Support\WeeklySchedule::formatDay($from),
                'to'    => \App\Support\WeeklySchedule::formatDay($to),
            ];
        }

        return $rows;
    }

    public function approve(User $reviewer): void
    {
        if ($this->action === 'create') {
            $payload = array_merge($this->payload, ['salon_id' => $this->salon_id]);
            $this->subject_type::create($payload);
        } elseif ($this->action === 'delete') {
            $this->subject?->delete();
        } else {
            $this->subject?->update($this->payload);
        }

        $this->update([
            'status'      => 'approved',
            'reviewed_by' => $reviewer->id,
            'reviewed_at' => now(),
        ]);
    }

    public function reject(User $reviewer, ?string $notes = null): void
    {
        $this->update([
            'status'       => 'rejected',
            'reviewed_by'  => $reviewer->id,
            'reviewed_at'  => now(),
            'review_notes' => $notes,
        ]);
    }
}
