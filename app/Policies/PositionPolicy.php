<?php

namespace App\Policies;

use App\Models\Position;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PositionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengakses manajemen jabatan.');
    }

    public function view(User $user, Position $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat melihat detail jabatan.');
    }

    public function create(User $user): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menambah jabatan baru.');
    }

    public function update(User $user, Position $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat mengubah data jabatan.');
    }

    public function delete(User $user, Position $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menghapus jabatan.');
    }

    public function restore(User $user, Position $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat memulihkan jabatan.');
    }

    public function forceDelete(User $user, Position $model): Response
    {
        return $user->isAdmin() 
            ? Response::allow() 
            : Response::deny('Hanya Admin yang dapat menghapus permanen jabatan.');
    }
}
