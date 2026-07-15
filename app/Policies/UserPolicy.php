<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengakses manajemen pengguna.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat melihat detail pengguna.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menambah pengguna baru.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        if (!$user->isAdmin()) {
            return Response::deny('Hanya Admin yang dapat mengubah data pengguna.');
        }

        return $user->id !== $model->id 
            ? Response::allow() 
            : Response::deny('Anda tidak dapat mengubah akun sendiri melalui manajemen pengguna. Gunakan halaman profil.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        if (!$user->isAdmin()) {
            return Response::deny('Hanya Admin yang dapat menghapus pengguna.');
        }

        return $user->id !== $model->id 
            ? Response::allow() 
            : Response::deny('Anda tidak dapat menghapus akun Anda sendiri.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        if (!$user->isAdmin()) {
            return Response::deny('Hanya Admin yang dapat memulihkan pengguna.');
        }

        return $user->id !== $model->id 
            ? Response::allow() 
            : Response::deny('Anda tidak dapat memulihkan akun Anda sendiri.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        if (!$user->isAdmin()) {
            return Response::deny('Hanya Admin yang dapat menghapus permanen pengguna.');
        }

        return $user->id !== $model->id 
            ? Response::allow() 
            : Response::deny('Anda tidak dapat menghapus permanen akun Anda sendiri.');
    }
}
