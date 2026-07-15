<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    public function viewAny(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager', 'staff']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk melihat daftar absensi.');
    }

    public function view(User $user, Attendance $model): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager', 'staff']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk melihat detail absensi.');
    }

    public function create(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menambah data absensi baru.');
    }

    public function update(User $user, Attendance $model): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk mengubah data absensi.');
    }

    public function delete(User $user, Attendance $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menghapus data absensi.');
    }

    public function restore(User $user, Attendance $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk memulihkan data absensi.');
    }

    public function forceDelete(User $user, Attendance $model): Response
    {
        return in_array($user->role, ['admin', 'HR']) 
            ? Response::allow() 
            : Response::deny('Anda tidak memiliki izin untuk menghapus permanen data absensi.');
    }
}
