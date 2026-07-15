<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    public function viewAny(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager', 'staff']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk melihat daftar karyawan.');
    }

    public function view(User $user, Employee $model): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager', 'staff']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk melihat detail karyawan.');
    }

    public function create(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menambah karyawan baru.');
    }

    public function update(User $user, Employee $model): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk mengubah data karyawan.');
    }

    public function delete(User $user, Employee $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menghapus karyawan.');
    }

    public function restore(User $user, Employee $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk memulihkan karyawan.');
    }

    public function forceDelete(User $user, Employee $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menghapus permanen karyawan.');
    }
}
