<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    public function viewAny(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk melihat daftar departemen.');
    }

    public function view(User $user, Department $model): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk melihat detail departemen.');
    }

    public function create(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menambah departemen baru.');
    }

    public function update(User $user, Department $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk mengubah data departemen.');
    }

    public function delete(User $user, Department $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menghapus departemen.');
    }

    public function restore(User $user, Department $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk memulihkan departemen.');
    }

    public function forceDelete(User $user, Department $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menghapus permanen departemen.');
    }
}
