<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SalonScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Not authenticated (console, seeders, queued jobs, guest routes): no request-scoped
        // salon to restrict to, so leave the query alone rather than filtering everything out.
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        // Platform admin: intentionally unrestricted.
        if ($user->isSuperAdmin()) {
            return;
        }

        $builder->where($model->getTable() . '.salon_id', $user->salon_id);
    }
}
