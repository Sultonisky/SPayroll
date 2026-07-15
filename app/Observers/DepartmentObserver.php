<?php

namespace App\Observers;

use App\Models\Department;
use App\Models\User;
use App\Notifications\DashboardNotification;

class DepartmentObserver
{
    public function created(Department $department): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'New Department',
                "New department '{$department->name}' has been created.",
                route('departments.show', $department->id),
                'info'
            ));
        }
    }

    public function updated(Department $department): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Department Updated',
                "Department '{$department->name}' has been updated.",
                route('departments.show', $department->id),
                'warning'
            ));
        }
    }

    public function deleted(Department $department): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Department Deleted',
                "Department '{$department->name}' has been deleted.",
                route('departments.index'),
                'danger'
            ));
        }
    }

    public function restored(Department $department): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Department Restored',
                "Department '{$department->name}' has been restored.",
                route('departments.show', $department->id),
                'success'
            ));
        }
    }

    public function forceDeleted(Department $department): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Department Permanently Deleted',
                "Department '{$department->name}' has been permanently deleted.",
                route('departments.index'),
                'danger'
            ));
        }
    }
}