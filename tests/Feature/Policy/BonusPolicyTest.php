<?php

namespace Tests\Feature\Policy;

use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for BonusPolicy — including isPending guards.
 *
 * NOTE: Gate::before() grants non-demo admin ALL abilities unconditionally.
 * Tests that verify the isPending() guard must therefore use demoAdmin()
 * (which goes through normal policy evaluation) or non-admin roles.
 */
class BonusPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function makeBonus(string $status = 'pending'): Bonus
    {
        $dept = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime' => 5_000_000,
            'base_salary_internship' => 2_000_000,
        ]);
        $employee = Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $position->id,
        ]);

        if ($status === 'pending') {
            return Bonus::factory()->pending()->create(['employee_id' => $employee->id]);
        }

        if ($status === 'approved') {
            return Bonus::factory()->approved()->create(['employee_id' => $employee->id]);
        }

        // rejected — safe date for MySQL
        return Bonus::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'rejected',
            'approved_at' => now()->subDays(5),
            'notes' => 'Rejected for test',
        ]);
    }

    // -----------------------------------------------------------------------
    // viewAny / view / create — admin, HR, manager
    // -----------------------------------------------------------------------

    public function test_admin_hr_manager_can_view_bonuses(): void
    {
        $bonus = $this->makeBonus();

        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->assertTrue($user->can('viewAny', Bonus::class));
            $this->assertTrue($user->can('view', $bonus));
        }
    }

    public function test_staff_cannot_view_bonuses(): void
    {
        $bonus = $this->makeBonus();

        $staff = $this->staffUser();
        $this->assertFalse($staff->can('viewAny', Bonus::class));
        $this->assertFalse($staff->can('view', $bonus));
    }

    // -----------------------------------------------------------------------
    // update — only pending bonuses
    // Gate::before bypasses policy for non-demo admin, so use demoAdmin / HR / manager
    // -----------------------------------------------------------------------

    public function test_manager_can_update_pending_bonus(): void
    {
        $bonus = $this->makeBonus('pending');

        $this->assertTrue($this->managerUser()->can('update', $bonus));
    }

    public function test_nobody_can_update_approved_bonus(): void
    {
        $bonus = $this->makeBonus('approved');

        // demoAdmin goes through real policy checks (not bypassed by Gate::before)
        $this->assertFalse($this->demoAdmin()->can('update', $bonus));
        $this->assertFalse($this->hrUser()->can('update', $bonus));
        $this->assertFalse($this->managerUser()->can('update', $bonus));
    }

    public function test_nobody_can_update_rejected_bonus(): void
    {
        $bonus = $this->makeBonus('rejected');

        $this->assertFalse($this->demoAdmin()->can('update', $bonus));
        $this->assertFalse($this->hrUser()->can('update', $bonus));
        $this->assertFalse($this->managerUser()->can('update', $bonus));
    }

    // -----------------------------------------------------------------------
    // approve — admin and HR only, only pending
    // -----------------------------------------------------------------------

    public function test_admin_and_hr_can_approve_pending_bonus(): void
    {
        $bonus = $this->makeBonus('pending');

        $this->assertTrue($this->adminUser()->can('approve', $bonus));
        $this->assertTrue($this->hrUser()->can('approve', $bonus));
    }

    public function test_manager_cannot_approve_bonus(): void
    {
        $bonus = $this->makeBonus('pending');

        $this->assertFalse($this->managerUser()->can('approve', $bonus));
    }

    public function test_nobody_can_approve_already_approved_bonus(): void
    {
        $bonus = $this->makeBonus('approved');

        // demoAdmin + HR: both go through the policy isPending() check
        $this->assertFalse($this->demoAdmin()->can('approve', $bonus));
        $this->assertFalse($this->hrUser()->can('approve', $bonus));
    }

    public function test_nobody_can_approve_rejected_bonus(): void
    {
        $bonus = $this->makeBonus('rejected');

        $this->assertFalse($this->demoAdmin()->can('approve', $bonus));
        $this->assertFalse($this->hrUser()->can('approve', $bonus));
    }

    // -----------------------------------------------------------------------
    // forceDelete — demo account blocked
    // -----------------------------------------------------------------------

    public function test_demo_admin_cannot_force_delete_bonus(): void
    {
        $demo = $this->demoAdmin();
        $bonus = $this->makeBonus();

        $this->assertFalse($demo->can('forceDelete', $bonus));
    }

    public function test_non_demo_admin_can_force_delete_bonus(): void
    {
        $bonus = $this->makeBonus();

        $this->assertTrue($this->adminUser()->can('forceDelete', $bonus));
    }
}
