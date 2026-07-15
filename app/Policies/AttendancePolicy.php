<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengakses manajemen kehadiran.');
    }

    public function view(User $user, Attendance $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat melihat detail kehadiran.');
    }

    public function create(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menambah data kehadiran baru.');
    }

    public function update(User $user, Attendance $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengubah data kehadiran.');
    }

    public function delete(User $user, Attendance $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menghapus data kehadiran.');
    }

    public function restore(User $user, Attendance $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat memulihkan data kehadiran.');
    }

    public function forceDelete(User $user, Attendance $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menghapus permanen data kehadiran.');
    }
}
