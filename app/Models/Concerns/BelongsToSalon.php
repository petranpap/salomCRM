<?php

namespace App\Models\Concerns;

/**
 * Every query against a model using this trait is automatically restricted to the
 * authenticated user's salon (super_admin is exempt — platform-wide access is
 * intentional for that role). This is the single choke point for tenant isolation:
 * it protects route-model binding (show/edit/update/destroy), API endpoints, and
 * any query anywhere in the app — including ones a future controller forgets to
 * scope manually, which is exactly how the bug this trait fixes happened.
 *
 * Does NOT apply to the User model itself — see the note in SalonScope.
 */
trait BelongsToSalon
{
    public static function bootBelongsToSalon(): void
    {
        static::addGlobalScope(new \App\Models\Scopes\SalonScope);
    }
}
