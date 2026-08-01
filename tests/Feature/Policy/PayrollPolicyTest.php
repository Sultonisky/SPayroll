<?php

namespace Tests\Feature\Policy;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for PayrollPolicy.
 */
class PayrollPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function makePayroll(string $status = 'draft'): Payroll
    {
        $dept = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime' => 10_000_000,
            'base_salary_internship' => 2_000_000,
        ]);
        $employee = Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $position->id,
            'employee_status' => 'active',
        ]);

        return Payroll::factory()->create([
            'employee_id' => $employee->id,
            'status'      => $status,
        ]);
    }

    // -----------------------------------------------------------------------
    // viewAny / view — all four roles
    // -----------------------------------------------------------------------

    public function test_all_roles_can_view_payrolls(): void
    {
        $payroll = $this->makePayroll();

        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser(), $this->staffUser()] as $user) {
            $this->assertTrue($user->can('viewAny', Payroll::class), "viewAny failed for: {$user->role}");
            $this->assertTrue($user->can('view', $payroll), "view failed for: {$user->role}");
        }
    }

    // -----------------------------------------------------------------------
    // create / update — admin, HR, manager
    // -----------------------------------------------------------------------

    public function test_admin_hr_manager_can_create_payrolls(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->assertTrue($user->can('create', Payroll::class));
        }
    }

    public function test_staff_cannot_create_payrolls(): void
    {
        $this->assertFalse($this->staffUser()->can('create', Payroll::class));
    }

    public function test_admin_hr_manager_can_update_payrolls(): void
    {
        $payroll = $this->makePayroll();

        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->assertTrue($user->can('update', $payroll));
        }
    }

    public function test_staff_cannot_update_payrolls(): void
    {
        $payroll = $this->makePayroll();

        $this->assertFalse($this->staffUser()->can('update', $payroll));
    }

    // -----------------------------------------------------------------------
    // delete / restore / forceDelete — admin, HR only
    // -----------------------------------------------------------------------

    public function test_admin_and_hr_can_delete_payrolls(): void
    {
        $payroll = $this->makePayroll();

        $this->assertTrue($this->adminUser()->can('delete', $payroll));
        $this->assertTrue($this->hrUser()->can('delete', $payroll));
    }

    public function test_manager_cannot_delete_payrolls(): void
    {
        $payroll = $this->makePayroll();

        $this->assertFalse($this->managerUser()->can('delete', $payroll));
    }

    public function test_staff_cannot_delete_payrolls(): void
    {
        $payroll = $this->makePayroll();

        $this->assertFalse($this->staffUser()->can('delete', $payroll));
    }

    public function test_admin_and_hr_can_restore_and_force_delete_payrolls(): void
    {
        $payroll = $this->makePayroll();

        $this->assertTrue($this->adminUser()->can('restore', $payroll));
        $this->assertTrue($this->adminUser()->can('forceDelete', $payroll));
        $this->assertTrue($this->hrUser()->can('restore', $payroll));
        $this->assertTrue($this->hrUser()->can('forceDelete', $payroll));
    }

    // -----------------------------------------------------------------------
    // viewPayslip — admin / HR / finance = all; staff = own only; manager = denied
    // -----------------------------------------------------------------------

    public function test_admin_hr_finance_can_view_any_payslip(): void
    {
        $payroll = $this->makePayroll('paid');

        $financeUser = \App\Models\User::factory()->create(['role' => 'finance']);

        foreach ([$this->adminUser(), $this->hrUser(), $financeUser] as $user) {
            $this->assertTrue(
                $user->can('viewPayslip', $payroll),
                "viewPayslip should be allowed for role: {$user->role}"
            );
        }
    }

    public function test_staff_can_view_own_payslip_via_policy(): void
    {
        $dept     = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime'   => 10_000_000,
            'base_salary_internship' => 2_000_000,
        ]);
        $employee = Employee::factory()->create([
            'department_id'   => $dept->id,
            'position_id'     => $position->id,
            'employee_status' => 'active',
        ]);

        $staff = \App\Models\User::factory()->create(['role' => 'staff']);
        $employee->update(['user_id' => $staff->id]);
        $staff->load('employee');

        $payroll = Payroll::factory()->create([
            'employee_id'  => $employee->id,
            'status'       => 'paid',
            'year'         => 2025,
            'month'        => 6,
            'base_salary'  => 10_000_000,
            'bonus'        => 0,
            'total_salary' => 10_000_000,
            'pay_date'     => '2025-06-25',
        ]);

        $this->assertTrue($staff->can('viewPayslip', $payroll));
    }

    public function test_staff_cannot_view_another_employees_payslip_via_policy(): void
    {
        $payroll = $this->makePayroll('paid');

        // Staff not linked to $payroll->employee
        $staff = \App\Models\User::factory()->create(['role' => 'staff']);

        $this->assertFalse($staff->can('viewPayslip', $payroll));
    }

    public function test_manager_cannot_view_payslip_via_policy(): void
    {
        $payroll = $this->makePayroll('paid');

        $this->assertFalse($this->managerUser()->can('viewPayslip', $payroll));
    }
}
