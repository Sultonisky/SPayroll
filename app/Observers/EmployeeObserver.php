<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\DashboardNotification;

class EmployeeObserver
{
    public function created(Employee $employee): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'New Employee',
                "New employee '{$employee->name}' has been registered.",
                route('employees.show', $employee->id),
                'info'
            ));
        }
    }

    public function updated(Employee $employee): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Employee Updated',
                "Employee '{$employee->name}' has been updated.",
                route('employees.show', $employee->id),
                'warning'
            ));
        }
    }

    public function deleted(Employee $employee): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Employee Deleted',
                "Employee '{$employee->name}' has been deleted.",
                route('employees.index'),
                'danger'
            ));
        }
    }

    public function restored(Employee $employee): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Employee Restored',
                "Employee '{$employee->name}' has been restored.",
                route('employees.show', $employee->id),
                'success'
            ));
        }
    }

    public function forceDeleted(Employee $employee): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Employee Permanently Deleted',
                "Employee '{$employee->name}' has been permanently deleted.",
                route('employees.index'),
                'danger'
            ));
        }
    }
}