<?php

namespace App\Policies;

use App\Models\Bonus;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BonusPolicy
{
    public function viewAny(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager'])
            ? Response::allow()
            : Response::deny('You do not have permission to view bonuses.');
    }

    public function view(User $user, Bonus $bonus): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager'])
            ? Response::allow()
            : Response::deny('You do not have permission to view this bonus.');
    }

    public function create(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager'])
            ? Response::allow()
            : Response::deny('You do not have permission to create bonuses.');
    }

    public function update(User $user, Bonus $bonus): Response
    {
        // Only pending bonuses can be edited
        if (! $bonus->isPending()) {
            return Response::deny('Only pending bonuses can be edited.');
        }

        return in_array($user->role, ['admin', 'HR', 'manager'])
            ? Response::allow()
            : Response::deny('You do not have permission to edit bonuses.');
    }

    public function approve(User $user, Bonus $bonus): Response
    {
        if (! $bonus->isPending()) {
            return Response::deny('Only pending bonuses can be approved or rejected.');
        }

        return in_array($user->role, ['admin', 'HR'])
            ? Response::allow()
            : Response::deny('Only Admin or HR can approve bonuses.');
    }

    public function delete(User $user, Bonus $bonus): Response
    {
        return in_array($user->role, ['admin', 'HR'])
            ? Response::allow()
            : Response::deny('You do not have permission to delete bonuses.');
    }

    public function restore(User $user, Bonus $bonus): Response
    {
        return in_array($user->role, ['admin', 'HR'])
            ? Response::allow()
            : Response::deny('You do not have permission to restore bonuses.');
    }

    public function forceDelete(User $user, Bonus $bonus): Response
    {
        return in_array($user->role, ['admin', 'HR'])
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete bonuses.');
    }
}
