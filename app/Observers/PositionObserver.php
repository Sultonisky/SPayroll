<?php

namespace App\Observers;

use App\Models\Position;
use App\Models\User;
use App\Notifications\DashboardNotification;

class PositionObserver
{
    public function created(Position $position): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'New Position',
                "New position '{$position->name}' has been created.",
                route('positions.show', $position->id),
                'info'
            ));
        }
    }

    public function updated(Position $position): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Position Updated',
                "Position '{$position->name}' has been updated.",
                route('positions.show', $position->id),
                'warning'
            ));
        }
    }

    public function deleted(Position $position): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Position Deleted',
                "Position '{$position->name}' has been deleted.",
                route('positions.index'),
                'danger'
            ));
        }
    }

    public function restored(Position $position): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Position Restored',
                "Position '{$position->name}' has been restored.",
                route('positions.show', $position->id),
                'success'
            ));
        }
    }

    public function forceDeleted(Position $position): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Position Permanently Deleted',
                "Position '{$position->name}' has been permanently deleted.",
                route('positions.index'),
                'danger'
            ));
        }
    }
}