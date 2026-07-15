<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PositionPolicy
{
    public function viewAny(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk melihat daftar jabatan.');
    }

    public function view(User $user, Position $model): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk melihat detail jabatan.');
    }

    public function create(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menambah jabatan baru.');
    }

    public function update(User $user, Position $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk mengubah data jabatan.');
    }

    public function delete(User $user, Position $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menghapus jabatan.');
    }

    public function restore(User $user, Position $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk memulihkan jabatan.');
    }

    public function forceDelete(User $user, Position $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menghapus permanen jabatan.');
    }
}
