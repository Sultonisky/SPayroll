<?php

namespace App\Observers;

use App\Models\Bonus;
use App\Models\User;
use App\Notifications\DashboardNotification;
use App\Services\AuditLogService;

class BonusObserver
{
    public function created(Bonus $bonus): void
    {
        AuditLogService::logModelEvent(
            'created',
            $bonus,
            "Bonus '{$bonus->type}' Rp ".number_format($bonus->amount, 0, ',', '.')." for employee #{$bonus->employee_id} submitted"
        );

        $admins = User::whereIn('role', ['admin', 'HR'])->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'New Bonus Submitted',
                "A new {$bonus->type} bonus of Rp ".number_format($bonus->amount, 0, ',', '.').
                " has been submitted for {$bonus->employee?->name}.",
                route('bonuses.show', $bonus->id),
                'info'
            ));
        }
    }

    public function updated(Bonus $bonus): void
    {
        // Determine a more specific action for status transitions
        $action = 'updated';
        $description = "Bonus #{$bonus->id} updated";

        if ($bonus->wasChanged('status')) {
            if ($bonus->isApproved()) {
                $action = 'approved';
                $description = "Bonus #{$bonus->id} '{$bonus->type}' approved for employee #{$bonus->employee_id}";
            } elseif ($bonus->isRejected()) {
                $action = 'rejected';
                $description = "Bonus #{$bonus->id} '{$bonus->type}' rejected for employee #{$bonus->employee_id}";
            }
        }

        AuditLogService::logModelEvent($action, $bonus, $description);

        // Notify employee on status change
        if ($bonus->wasChanged('status')) {
            $employee = $bonus->employee;
            if (! $employee) {
                return;
            }

            if ($bonus->isApproved()) {
                $employee->user?->notify(new DashboardNotification(
                    'Bonus Approved',
                    "Your {$bonus->type} bonus of Rp ".number_format($bonus->amount, 0, ',', '.').' has been approved.',
                    route('bonuses.show', $bonus->id),
                    'success'
                ));
            }

            if ($bonus->isRejected()) {
                $employee->user?->notify(new DashboardNotification(
                    'Bonus Rejected',
                    "Your {$bonus->type} bonus has been rejected.".($bonus->notes ? " Note: {$bonus->notes}" : ''),
                    route('bonuses.show', $bonus->id),
                    'danger'
                ));
            }
        }
    }

    public function deleted(Bonus $bonus): void
    {
        AuditLogService::log(
            'deleted',
            $bonus,
            "Bonus #{$bonus->id} '{$bonus->type}' moved to trash"
        );

        $admins = User::whereIn('role', ['admin', 'HR'])->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Bonus Deleted',
                "Bonus for {$bonus->employee?->name} has been moved to trash.",
                route('bonuses.index'),
                'warning'
            ));
        }
    }

    public function restored(Bonus $bonus): void
    {
        AuditLogService::log(
            'restored',
            $bonus,
            "Bonus #{$bonus->id} '{$bonus->type}' restored from trash"
        );
    }

    public function forceDeleted(Bonus $bonus): void
    {
        AuditLogService::log(
            'force_deleted',
            $bonus,
            "Bonus #{$bonus->id} '{$bonus->type}' permanently deleted"
        );
    }
}
