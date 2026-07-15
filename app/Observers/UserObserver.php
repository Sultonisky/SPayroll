<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\DashboardNotification;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Notify other admins about new user
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'New User Registered',
                "User '{$user->name}' has been added with role '{$user->role}'.",
                route('users.show', $user->id),
                'info'
            ));
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Notify other admins about updated user
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'User Updated',
                "User '{$user->name}' has been updated with role '{$user->role}'.",
                route('users.show', $user->id),
                'warning'
            ));
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Notify other admins about deleted user
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'User Deleted',
                "User '{$user->name}' has been deleted.",
                route('users.index'),
                'danger'
            ));
        }
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        // Notify other admins about restored user
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'User Restored',
                "User '{$user->name}' has been restored.",
                route('users.show', $user->id),
                'success'
            ));
        }
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        // Notify other admins about force deleted user
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'User Permanently Deleted',
                "User '{$user->name}' has been permanently deleted.",
                route('users.index'),
                'danger'
            ));
        }
    }
}
