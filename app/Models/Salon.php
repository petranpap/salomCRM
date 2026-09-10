<?php

namespace App\Models;

use App\Support\WeeklySchedule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Salon extends Model
{
    use HasFactory;

    const RECEIPT_TEMPLATES = ['classic', 'modern', 'minimal'];

    // Falls back to the app's own brand colors when a salon hasn't picked its own.
    const DEFAULT_RECEIPT_PRIMARY_COLOR   = '#7d523c';
    const DEFAULT_RECEIPT_SECONDARY_COLOR = '#2f2a25';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'vat_number',
        'vat_rate',
        'opening_hours',
        'timezone',
        'is_active',
        'sms_driver',
        'sms_credentials',
        'logo_path',
        'receipt_primary_color',
        'receipt_secondary_color',
        'receipt_template',
        'onboarding_completed_at',
    ];

    protected $casts = [
        'opening_hours'           => 'array',
        'is_active'               => 'boolean',
        'sms_credentials'         => 'encrypted:array',
        'onboarding_completed_at' => 'datetime',
    ];

    public function hasVat(): bool
    {
        return ! is_null($this->vat_rate);
    }

    public function needsOnboarding(): bool
    {
        return is_null($this->onboarding_completed_at);
    }

    /**
     * Root-relative, not Storage::url()'s absolute APP_URL-based one — the app is
     * commonly browsed from a host/port that doesn't match a stale APP_URL (e.g.
     * `php artisan serve`'s 127.0.0.1:8000 vs an APP_URL of plain http://localhost),
     * which would silently break the logo image for anyone hitting that mismatch.
     * A root-relative path always resolves against whatever origin actually served
     * the page.
     */
    public function logoUrl(): ?string
    {
        return $this->logo_path ? '/storage/' . ltrim($this->logo_path, '/') : null;
    }

    /**
     * Base64 data URI so the logo renders in the printed PDF regardless of the
     * server's dompdf remote/local-file-access configuration.
     *
     * Returns null (receipt renders without a logo, same as an unconfigured one)
     * rather than embedding the image, if neither GD nor Imagick is available —
     * dompdf needs one of those to decode a raster image, and without this guard
     * a server missing both would fail to print *every* receipt for any salon
     * that has a logo set, not just omit the logo.
     */
    public function logoDataUri(): ?string
    {
        if (! $this->logo_path || ! Storage::disk('public')->exists($this->logo_path)) {
            return null;
        }

        if (! extension_loaded('gd') && ! extension_loaded('imagick')) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($this->logo_path) ?: 'image/png';
        $contents = base64_encode(Storage::disk('public')->get($this->logo_path));

        return "data:{$mime};base64,{$contents}";
    }

    public function receiptPrimaryColor(): string
    {
        return $this->receipt_primary_color ?: self::DEFAULT_RECEIPT_PRIMARY_COLOR;
    }

    public function receiptSecondaryColor(): string
    {
        return $this->receipt_secondary_color ?: self::DEFAULT_RECEIPT_SECONDARY_COLOR;
    }

    public function normalizedOpeningHours(): array
    {
        return WeeklySchedule::normalize($this->opening_hours);
    }

    public function isOpenOn(string $day): bool
    {
        return $this->normalizedOpeningHours()[$day]['active'] ?? false;
    }

    public function fullCalendarBusinessHours(): array
    {
        return WeeklySchedule::toFullCalendarBusinessHours($this->normalizedOpeningHours());
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function owner()
    {
        return $this->hasOne(User::class)->where('role', 'owner');
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function serviceCategories()
    {
        return $this->hasMany(ServiceCategory::class);
    }

    public function staffMembers()
    {
        return $this->hasMany(User::class)->whereIn('role', User::SALON_ROLES);
    }
}
