<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat mengakses manajemen pengguna.');
    }

    public function view(User $user, User $model): Response
    {
        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat melihat detail pengguna.');
    }

    public function create(User $user): Response
    {
        if ($user->isDemo()) {
            return Response::deny('Demo accounts cannot create users.');
        }

        return $user->isAdmin()
            ? Response::allow()
            : Response::deny('Hanya Admin yang dapat menambah pengguna baru.');
    }

    public function update(User $user, User $model): Response
    {
        if ($user->isDemo()) {
            return Response::deny('Demo accounts cannot modify users.');
        }
        if (! $user->isAdmin()) {
            return Response::deny('Hanya Admin yang dapat mengubah data pengguna.');
        }

        return $user->id !== $model->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat mengubah akun sendiri melalui manajemen pengguna. Gunakan halaman profil.');
    }

    public function delete(User $user, User $model): Response
    {
        if ($user->isDemo()) {
            return Response::deny('Demo accounts cannot delete users.');
        }
        if (! $user->isAdmin()) {
            return Response::deny('Hanya Admin yang dapat menghapus pengguna.');
        }

        return $user->id !== $model->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat menghapus akun Anda sendiri.');
    }

    public function restore(User $user, User $model): Response
    {
        if ($user->isDemo()) {
            return Response::deny('Demo accounts cannot restore users.');
        }
        if (! $user->isAdmin()) {
            return Response::deny('Hanya Admin yang dapat memulihkan pengguna.');
        }

        return $user->id !== $model->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat memulihkan akun Anda sendiri.');
    }

    public function forceDelete(User $user, User $model): Response
    {
        if ($user->isDemo()) {
            return Response::deny('Demo accounts cannot permanently delete users.');
        }
        if (! $user->isAdmin()) {
            return Response::deny('Hanya Admin yang dapat menghapus permanen pengguna.');
        }

        return $user->id !== $model->id
            ? Response::allow()
            : Response::deny('Anda tidak dapat menghapus permanen akun Anda sendiri.');
    }
}
