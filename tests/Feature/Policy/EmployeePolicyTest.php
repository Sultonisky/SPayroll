<?php

namespace Tests\Feature\Policy;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for EmployeePolicy.
 */
class EmployeePolicyTest extends TestCase
{
    use RefreshDatabase;

    private function makeEmployee(): Employee
    {
        return Employee::factory()->create([
            'department_id' => Department::factory()->create()->id,
            'position_id' => Position::factory()->create([
                'base_salary_fulltime' => 5_000_000,
                'base_salary_internship' => 2_000_000,
            ])->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // viewAny / view — all roles allowed
    // -----------------------------------------------------------------------

    public function test_all_roles_can_view_any_employees(): void
    {
        $employee = $this->makeEmployee();

        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser(), $this->staffUser()] as $user) {
            $this->assertTrue($user->can('viewAny', Employee::class), "Failed for role: {$user->role}");
        }
    }

    public function test_all_roles_can_view_employee_detail(): void
    {
        $employee = $this->makeEmployee();

        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser(), $this->staffUser()] as $user) {
            $this->assertTrue($user->can('view', $employee));
        }
    }

    // -----------------------------------------------------------------------
    // create / update — admin, HR, manager
    // -----------------------------------------------------------------------

    public function test_admin_hr_manager_can_create_employees(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->assertTrue($user->can('create', Employee::class));
        }
    }

    public function test_staff_cannot_create_employees(): void
    {
        $this->assertFalse($this->staffUser()->can('create', Employee::class));
    }

    public function test_admin_hr_manager_can_update_employees(): void
    {
        $employee = $this->makeEmployee();

        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->assertTrue($user->can('update', $employee));
        }
    }

    public function test_staff_cannot_update_employees(): void
    {
        $employee = $this->makeEmployee();

        $this->assertFalse($this->staffUser()->can('update', $employee));
    }

    // -----------------------------------------------------------------------
    // delete / restore — admin, HR only
    // -----------------------------------------------------------------------

    public function test_admin_and_hr_can_delete_employees(): void
    {
        $employee = $this->makeEmployee();

        $this->assertTrue($this->adminUser()->can('delete', $employee));
        $this->assertTrue($this->hrUser()->can('delete', $employee));
    }

    public function test_manager_cannot_delete_employees(): void
    {
        $employee = $this->makeEmployee();

        $this->assertFalse($this->managerUser()->can('delete', $employee));
    }

    public function test_staff_cannot_delete_employees(): void
    {
        $employee = $this->makeEmployee();

        $this->assertFalse($this->staffUser()->can('delete', $employee));
    }

    // -----------------------------------------------------------------------
    // forceDelete — demo account blocked
    // -----------------------------------------------------------------------

    public function test_demo_admin_cannot_force_delete_employee(): void
    {
        $demo = $this->demoAdmin();
        $employee = $this->makeEmployee();

        $this->assertFalse($demo->can('forceDelete', $employee));
    }

    public function test_non_demo_hr_can_force_delete_employee(): void
    {
        $employee = $this->makeEmployee();

        $this->assertTrue($this->hrUser()->can('forceDelete', $employee));
    }
}
