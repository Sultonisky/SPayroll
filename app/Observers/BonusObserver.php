<?php

namespace App\Observers;

use App\Models\Bonus;
use App\Models\User;
use App\Notifications\DashboardNotification;

class BonusObserver
{
    public function created(Bonus $bonus): void
    {
        $admins = User::whereIn('role', ['admin', 'HR'])->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'New Bonus Submitted',
                "A new {$bonus->type} bonus of Rp " . number_format($bonus->amount, 0, ',', '.') .
                " has been submitted for {$bonus->employee?->name}.",
                route('bonuses.show', $bonus->id),
                'info'
            ));
        }
    }

    public function updated(Bonus $bonus): void
    {
        // Notify when status changes to approved or rejected
        if ($bonus->wasChanged('status')) {
            $employee = $bonus->employee;
            if (! $employee) return;

            if ($bonus->isApproved()) {
                $employee->user?->notify(new DashboardNotification(
                    'Bonus Approved',
                    "Your {$bonus->type} bonus of Rp " . number_format($bonus->amount, 0, ',', '.') . " has been approved.",
                    route('bonuses.show', $bonus->id),
                    'success'
                ));
            }

            if ($bonus->isRejected()) {
                $employee->user?->notify(new DashboardNotification(
                    'Bonus Rejected',
                    "Your {$bonus->type} bonus has been rejected." . ($bonus->notes ? " Note: {$bonus->notes}" : ''),
                    route('bonuses.show', $bonus->id),
                    'danger'
                ));
            }
        }
    }

    public function deleted(Bonus $bonus): void
    {
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
}
