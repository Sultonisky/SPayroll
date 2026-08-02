<?php

namespace App\Observers;

use App\Models\Payroll;
use App\Models\User;
use App\Notifications\DashboardNotification;
use Illuminate\Database\Eloquent\Collection;

class PayrollObserver
{
    /**
     * Users responsible for the payroll engine: admin, HR, finance.
     * Excludes the currently authenticated user to avoid self-notification.
     */
    private function payrollManagers(): Collection
    {
        return User::whereIn('role', ['admin', 'HR', 'finance'])
            ->where('id', '!=', auth()->id() ?? 0)
            ->get();
    }

    public function created(Payroll $payroll): void
    {
        $period = $payroll->monthName();
        $name = $payroll->employee?->name ?? 'Unknown';

        foreach ($this->payrollManagers() as $user) {
            $user->notify(new DashboardNotification(
                'New Payroll Draft Created',
                "Draft payroll for {$name} ({$period}) has been created.",
                route('payrolls.drafts'),
                'info'
            ));
        }
    }

    public function updated(Payroll $payroll): void
    {
        $period = $payroll->monthName();
        $name = $payroll->employee?->name ?? 'Unknown';

        // Status-specific notifications for approve and paid transitions
        if ($payroll->wasChanged('status')) {
            $newStatus = $payroll->status;

            if ($newStatus === 'approved') {
                foreach ($this->payrollManagers() as $user) {
                    $user->notify(new DashboardNotification(
                        'Payroll Approved',
                        "Payroll for {$name} ({$period}) has been approved.",
                        route('payrolls.approved'),
                        'success'
                    ));
                }

                return;
            }

            if ($newStatus === 'paid') {
                foreach ($this->payrollManagers() as $user) {
                    $user->notify(new DashboardNotification(
                        'Payroll Marked as Paid',
                        "Payroll for {$name} ({$period}) has been marked as paid.",
                        route('payrolls.index'),
                        'success'
                    ));
                }

                return;
            }
        }

        // Generic update notification
        foreach ($this->payrollManagers() as $user) {
            $user->notify(new DashboardNotification(
                'Payroll Record Updated',
                "Payroll for {$name} ({$period}) has been updated.",
                route('payrolls.show', $payroll->id),
                'warning'
            ));
        }
    }

    public function deleted(Payroll $payroll): void
    {
        $period = $payroll->monthName();
        $name = $payroll->employee?->name ?? 'Unknown';

        foreach ($this->payrollManagers() as $user) {
            $user->notify(new DashboardNotification(
                'Payroll Record Deleted',
                "Payroll for {$name} ({$period}) has been moved to trash.",
                route('payrolls.trash'),
                'danger'
            ));
        }
    }

    public function restored(Payroll $payroll): void
    {
        $period = $payroll->monthName();
        $name = $payroll->employee?->name ?? 'Unknown';

        foreach ($this->payrollManagers() as $user) {
            $user->notify(new DashboardNotification(
                'Payroll Record Restored',
                "Payroll for {$name} ({$period}) has been restored from trash.",
                route('payrolls.show', $payroll->id),
                'success'
            ));
        }
    }

    public function forceDeleted(Payroll $payroll): void
    {
        $period = $payroll->monthName();
        $name = $payroll->employee?->name ?? 'Unknown';

        foreach ($this->payrollManagers() as $user) {
            $user->notify(new DashboardNotification(
                'Payroll Permanently Deleted',
                "Payroll for {$name} ({$period}) has been permanently deleted.",
                route('payrolls.index'),
                'danger'
            ));
        }
    }
}
