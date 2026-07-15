<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengakses manajemen departemen.');
    }

    public function view(User $user, Department $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat melihat detail departemen.');
    }

    public function create(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menambah departemen baru.');
    }

    public function update(User $user, Department $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengubah data departemen.');
    }

    public function delete(User $user, Department $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menghapus departemen.');
    }

    public function restore(User $user, Department $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat memulihkan departemen.');
    }

    public function forceDelete(User $user, Department $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menghapus permanen departemen.');
    }
}
