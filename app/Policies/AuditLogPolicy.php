<?php

namespace App\Policies;

use App\Models\AuditLog;
use App\Models\User;

class AuditLogPolicy
{
    /**
     * Only admins (non-demo) can view audit logs.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() && ! $user->isDemo();
    }

    public function view(User $user, AuditLog $auditLog): bool
    {
        return $user->isAdmin() && ! $user->isDemo();
    }
}
