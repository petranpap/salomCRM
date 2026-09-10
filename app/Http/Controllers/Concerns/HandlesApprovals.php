<?php

namespace App\Http\Controllers\Concerns;

use App\Models\PendingChange;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Owners write directly; anyone else (staff) has their create/update queued as a
 * PendingChange for the owner to approve instead of applying immediately.
 */
trait HandlesApprovals
{
    protected function isApprover(User $user): bool
    {
        return $user->isOwner() || $user->isSuperAdmin();
    }

    protected function applyOrQueue(string $modelClass, array $data, ?Model $existing, User $user): string
    {
        if ($this->isApprover($user)) {
            if ($existing) {
                $existing->update($data);
            } else {
                $modelClass::create($data);
            }

            return $existing ? 'updated' : 'created';
        }

        PendingChange::create([
            'salon_id'     => $user->salon_id,
            'subject_type' => $modelClass,
            'subject_id'   => $existing?->id,
            'action'       => $existing ? 'update' : 'create',
            'payload'      => $data,
            'submitted_by' => $user->id,
        ]);

        return 'queued';
    }

    protected function deleteOrQueue(Model $existing, User $user): string
    {
        if ($this->isApprover($user)) {
            $existing->delete();

            return 'deleted';
        }

        PendingChange::create([
            'salon_id'     => $user->salon_id,
            'subject_type' => get_class($existing),
            'subject_id'   => $existing->id,
            'action'       => 'delete',
            'payload'      => [],
            'submitted_by' => $user->id,
        ]);

        return 'queued';
    }
}
