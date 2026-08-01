<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PayrollPolicy
{
    public function viewAny(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager', 'staff', 'finance'])
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk melihat daftar penggajian.');
    }

    public function view(User $user, Payroll $model): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager', 'staff', 'finance'])
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk melihat detail penggajian.');
    }

    /**
     * Payslip access:
     * - admin, HR, finance: can view all payslips
     * - staff: can only view their own (enforced in controller)
     * - manager: no access — salary figures are need-to-know for HR/Finance only
     */
    public function viewPayslip(User $user, Payroll $model): Response
    {
        if (in_array($user->role, ['admin', 'HR', 'finance'])) {
            return Response::allow();
        }

        if ($user->role === 'staff' && $user->employee?->id === $model->employee_id) {
            return Response::allow();
        }

        return Response::deny('Anda tidak memiliki izin untuk melihat payslip ini.');
    }

    public function create(User $user): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager', 'finance'])
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk menambah data penggajian baru.');
    }

    public function update(User $user, Payroll $model): Response
    {
        return in_array($user->role, ['admin', 'HR', 'manager', 'finance'])
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk mengubah data penggajian.');
    }

    public function delete(User $user, Payroll $model): Response
    {
        return in_array($user->role, ['admin', 'HR'])
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk menghapus data penggajian.');
    }

    public function restore(User $user, Payroll $model): Response
    {
        return in_array($user->role, ['admin', 'HR'])
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk memulihkan data penggajian.');
    }

    public function forceDelete(User $user, Payroll $model): Response
    {
        return in_array($user->role, ['admin', 'HR'])
            ? Response::allow()
            : Response::deny('Anda tidak memiliki izin untuk menghapus permanen data penggajian.');
    }
}
