<?php

namespace App\Observers;

use App\Models\Position;
use App\Models\User;
use App\Notifications\DashboardNotification;
use App\Services\AuditLogService;

class PositionObserver
{
    public function created(Position $position): void
    {
        AuditLogService::logModelEvent(
            'created',
            $position,
            "Position '{$position->name}' created"
        );

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
        AuditLogService::logModelEvent(
            'updated',
            $position,
            "Position '{$position->name}' updated"
        );

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
        AuditLogService::log(
            'deleted',
            $position,
            "Position '{$position->name}' moved to trash"
        );

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
        AuditLogService::log(
            'restored',
            $position,
            "Position '{$position->name}' restored from trash"
        );

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
        AuditLogService::log(
            'force_deleted',
            $position,
            "Position '{$position->name}' permanently deleted"
        );

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
