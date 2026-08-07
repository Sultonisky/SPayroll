<?php

namespace App\Observers;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\DashboardNotification;
use App\Services\AuditLogService;

class EmployeeObserver
{
    /**
     * Auto-generate employee_code before creating (format: 001, 002, ...)
     */
    public function creating(Employee $employee): void
    {
        if (empty($employee->employee_code)) {
            $latest = Employee::withTrashed()
                ->whereNotNull('employee_code')
                ->orderByDesc('employee_code')
                ->value('employee_code');

            $next = $latest ? ((int) $latest) + 1 : 1;
            $employee->employee_code = str_pad($next, 3, '0', STR_PAD_LEFT);
        }
    }

    public function created(Employee $employee): void
    {
        AuditLogService::logModelEvent(
            'created',
            $employee,
            "Employee '{$employee->name}' (#{$employee->employee_code}) created"
        );

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
        AuditLogService::logModelEvent(
            'updated',
            $employee,
            "Employee '{$employee->name}' (#{$employee->employee_code}) updated"
        );

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
        AuditLogService::log(
            'deleted',
            $employee,
            "Employee '{$employee->name}' moved to trash"
        );

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
        AuditLogService::log(
            'restored',
            $employee,
            "Employee '{$employee->name}' restored from trash"
        );

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
        AuditLogService::log(
            'force_deleted',
            $employee,
            "Employee '{$employee->name}' permanently deleted"
        );

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
