<?php

namespace App\Observers;

use App\Models\User;
use App\Notifications\DashboardNotification;
use App\Services\AuditLogService;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        AuditLogService::logModelEvent(
            'created',
            $user,
            "User '{$user->name}' (role: {$user->role}) created"
        );

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
        AuditLogService::logModelEvent(
            'updated',
            $user,
            "User '{$user->name}' (role: {$user->role}) updated"
        );

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
        AuditLogService::log(
            'deleted',
            $user,
            "User '{$user->name}' moved to trash"
        );

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
        AuditLogService::log(
            'restored',
            $user,
            "User '{$user->name}' restored from trash"
        );

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
        AuditLogService::log(
            'force_deleted',
            $user,
            "User '{$user->name}' permanently deleted"
        );

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
