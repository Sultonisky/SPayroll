<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengakses manajemen karyawan.');
    }

    public function view(User $user, Employee $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat melihat detail karyawan.');
    }

    public function create(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menambah karyawan baru.');
    }

    public function update(User $user, Employee $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengubah data karyawan.');
    }

    public function delete(User $user, Employee $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menghapus karyawan.');
    }

    public function restore(User $user, Employee $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat memulihkan karyawan.');
    }

    public function forceDelete(User $user, Employee $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menghapus permanen karyawan.');
    }
}
