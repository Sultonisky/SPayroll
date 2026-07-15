<?php

namespace App\Observers;

use App\Models\Payroll;
use App\Models\User;
use App\Notifications\DashboardNotification;

class PayrollObserver
{
    public function created(Payroll $payroll): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'New Payroll Record',
                "New payroll record for {$payroll->year}/{$payroll->month} has been created.",
                route('payrolls.show', $payroll->id),
                'info'
            ));
        }
    }

    public function updated(Payroll $payroll): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Payroll Record Updated',
                "Payroll record for {$payroll->year}/{$payroll->month} has been updated.",
                route('payrolls.show', $payroll->id),
                'warning'
            ));
        }
    }

    public function deleted(Payroll $payroll): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Payroll Record Deleted',
                "Payroll record for {$payroll->year}/{$payroll->month} has been deleted.",
                route('payrolls.index'),
                'danger'
            ));
        }
    }

    public function restored(Payroll $payroll): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Payroll Record Restored',
                "Payroll record for {$payroll->year}/{$payroll->month} has been restored.",
                route('payrolls.show', $payroll->id),
                'success'
            ));
        }
    }

    public function forceDeleted(Payroll $payroll): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Payroll Record Permanently Deleted',
                "Payroll record for {$payroll->year}/{$payroll->month} has been permanently deleted.",
                route('payrolls.index'),
                'danger'
            ));
        }
    }
}