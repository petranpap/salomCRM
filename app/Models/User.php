<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Roles: super_admin = platform owner (no salon_id), owner/staff = salon-scoped.
    // Job title/position (e.g. "Manager", "Receptionist") is a free-text label on
    // StaffProfile, separate from this system-level permission role.
    const ROLES = ['super_admin', 'owner', 'staff'];
    const SALON_ROLES = ['owner', 'staff'];

    const MAX_FAILED_LOGIN_ATTEMPTS = 3;
    const LOCKOUT_MINUTES = 30;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'salon_id',
        'must_change_password',
        'failed_login_attempts',
        'locked_until',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at'     => 'datetime',
        'must_change_password'  => 'boolean',
        'locked_until'          => 'datetime',
    ];

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    /**
     * Records one failed login attempt; locks the account once it hits the threshold.
     * Locking resets the counter too, so the lock always runs the full duration from
     * the attempt that triggered it rather than stacking on partial windows.
     */
    public function registerFailedLogin(): void
    {
        $attempts = $this->failed_login_attempts + 1;

        if ($attempts >= self::MAX_FAILED_LOGIN_ATTEMPTS) {
            $this->update([
                'failed_login_attempts' => 0,
                'locked_until'          => now()->addMinutes(self::LOCKOUT_MINUTES),
            ]);

            return;
        }

        $this->update(['failed_login_attempts' => $attempts]);
    }

    /**
     * Called on a successful login — a real sign-in clears any prior failed streak.
     */
    public function clearFailedLogins(): void
    {
        if ($this->failed_login_attempts > 0 || $this->locked_until !== null) {
            $this->update(['failed_login_attempts' => 0, 'locked_until' => null]);
        }
    }

    /**
     * Explicit admin action — lets the account back in immediately instead of waiting
     * out the lockout window.
     */
    public function unlock(): void
    {
        $this->update(['failed_login_attempts' => 0, 'locked_until' => null]);
    }

    public function staffProfile()
    {
        return $this->hasOne(StaffProfile::class);
    }

    public function salon()
    {
        return $this->belongsTo(Salon::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }
}