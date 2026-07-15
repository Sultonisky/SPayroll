<?php

namespace App\Observers;

use App\Models\Attendance;
use App\Models\User;
use App\Notifications\DashboardNotification;

class AttendanceObserver
{
    public function created(Attendance $attendance): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'New Attendance Record',
                "New attendance record for {$attendance->year}/{$attendance->month} has been created.",
                route('attendances.show', $attendance->id),
                'info'
            ));
        }
    }

    public function updated(Attendance $attendance): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Attendance Record Updated',
                "Attendance record for {$attendance->year}/{$attendance->month} has been updated.",
                route('attendances.show', $attendance->id),
                'warning'
            ));
        }
    }

    public function deleted(Attendance $attendance): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Attendance Record Deleted',
                "Attendance record for {$attendance->year}/{$attendance->month} has been deleted.",
                route('attendances.index'),
                'danger'
            ));
        }
    }

    public function restored(Attendance $attendance): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Attendance Record Restored',
                "Attendance record for {$attendance->year}/{$attendance->month} has been restored.",
                route('attendances.show', $attendance->id),
                'success'
            ));
        }
    }

    public function forceDeleted(Attendance $attendance): void
    {
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new DashboardNotification(
                'Attendance Record Permanently Deleted',
                "Attendance record for {$attendance->year}/{$attendance->month} has been permanently deleted.",
                route('attendances.index'),
                'danger'
            ));
        }
    }
}