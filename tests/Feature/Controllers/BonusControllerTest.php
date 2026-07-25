<?php

namespace Tests\Feature\Controllers;

use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for BonusController — CRUD, approve/reject, RBAC.
 */
class BonusControllerTest extends TestCase
{
    use RefreshDatabase;

    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $dept = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime' => 8_000_000,
            'base_salary_internship' => 2_000_000,
        ]);
        $this->employee = Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $position->id,
            'employee_status' => 'active',
        ]);
    }

    private function makeBonus(string $status = 'pending'): Bonus
    {
        if ($status === 'pending') {
            return Bonus::factory()->pending()->create([
                'employee_id' => $this->employee->id,
                'year' => 2025,
                'month' => 6,
            ]);
        }

        if ($status === 'approved') {
            return Bonus::factory()->approved()->create([
                'employee_id' => $this->employee->id,
                'year' => 2025,
                'month' => 6,
            ]);
        }

        // rejected — safe date for MySQL
        return Bonus::factory()->create([
            'employee_id' => $this->employee->id,
            'year' => 2025,
            'month' => 6,
            'status' => 'rejected',
            'approved_at' => now()->subDays(5),
            'notes' => 'Rejected for test',
        ]);
    }

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function test_admin_hr_manager_can_view_bonuses_index(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->actingAs($user)->get(route('bonuses.index'))->assertOk();
        }
    }

    public function test_staff_cannot_view_bonuses_index(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('bonuses.index'))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function test_admin_can_create_bonus(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('bonuses.create'))
            ->assertOk();
    }

    public function test_admin_can_store_bonus(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('bonuses.store'), [
                'employee_id' => $this->employee->id,
                'year' => 2025,
                'month' => 8,
                'type' => 'Performance Bonus',
                'amount' => 1_000_000,
                'description' => 'Great work',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bonuses', [
            'employee_id' => $this->employee->id,
            'type' => 'Performance Bonus',
            'status' => 'pending',
        ]);
    }

    public function test_store_fails_without_required_fields(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('bonuses.store'), [])
            ->assertSessionHasErrors(['employee_id', 'year', 'month', 'type', 'amount']);
    }

    public function test_staff_cannot_create_bonus(): void
    {
        $this->actingAs($this->staffUser())
            ->post(route('bonuses.store'), [
                'employee_id' => $this->employee->id,
                'year' => 2025,
                'month' => 8,
                'type' => 'Bonus',
                'amount' => 500_000,
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Show
    // -----------------------------------------------------------------------

    public function test_admin_can_view_bonus_detail(): void
    {
        $bonus = $this->makeBonus();

        $this->actingAs($this->adminUser())
            ->get(route('bonuses.show', $bonus->id))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // Update — only pending
    // -----------------------------------------------------------------------

    public function test_manager_can_update_pending_bonus(): void
    {
        $bonus = $this->makeBonus('pending');

        $this->actingAs($this->managerUser())
            ->put(route('bonuses.update', $bonus->id), [
                'employee_id' => $this->employee->id,
                'year' => 2025,
                'month' => 6,
                'type' => 'Annual Bonus',
                'amount' => 2_000_000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bonuses', ['id' => $bonus->id, 'type' => 'Annual Bonus']);
    }

    public function test_nobody_can_update_approved_bonus(): void
    {
        $bonus = $this->makeBonus('approved');

        // Non-demo admin bypasses Gate::before → use HR and manager who go through policy
        foreach ([$this->hrUser(), $this->managerUser()] as $user) {
            $this->actingAs($user)
                ->put(route('bonuses.update', $bonus->id), [
                    'employee_id' => $this->employee->id,
                    'year' => 2025,
                    'month' => 6,
                    'type' => 'Changed',
                    'amount' => 1,
                ])
                ->assertForbidden();
        }
    }

    // -----------------------------------------------------------------------
    // Approve / Reject
    // -----------------------------------------------------------------------

    public function test_admin_can_approve_pending_bonus(): void
    {
        $bonus = $this->makeBonus('pending');

        $this->actingAs($this->adminUser())
            ->post(route('bonuses.approve', $bonus->id))
            ->assertRedirect();

        $this->assertDatabaseHas('bonuses', ['id' => $bonus->id, 'status' => 'approved']);
    }

    public function test_hr_can_approve_pending_bonus(): void
    {
        $bonus = $this->makeBonus('pending');

        $this->actingAs($this->hrUser())
            ->post(route('bonuses.approve', $bonus->id))
            ->assertRedirect();

        $this->assertDatabaseHas('bonuses', ['id' => $bonus->id, 'status' => 'approved']);
    }

    public function test_manager_cannot_approve_bonus(): void
    {
        $bonus = $this->makeBonus('pending');

        $this->actingAs($this->managerUser())
            ->post(route('bonuses.approve', $bonus->id))
            ->assertForbidden();
    }

    public function test_admin_can_reject_pending_bonus(): void
    {
        $bonus = $this->makeBonus('pending');

        $this->actingAs($this->adminUser())
            ->post(route('bonuses.reject', $bonus->id), [
                'notes' => 'Not meeting criteria',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('bonuses', ['id' => $bonus->id, 'status' => 'rejected']);
    }

    public function test_cannot_approve_already_approved_bonus(): void
    {
        $bonus = $this->makeBonus('approved');

        // Non-demo admin bypasses Gate::before — use HR who goes through policy
        $this->actingAs($this->hrUser())
            ->post(route('bonuses.approve', $bonus->id))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Soft delete / Restore
    // -----------------------------------------------------------------------

    public function test_admin_can_delete_bonus(): void
    {
        $bonus = $this->makeBonus();

        $this->actingAs($this->adminUser())
            ->delete(route('bonuses.destroy', $bonus->id))
            ->assertRedirect();

        $this->assertSoftDeleted('bonuses', ['id' => $bonus->id]);
    }

    public function test_admin_can_restore_bonus(): void
    {
        $bonus = $this->makeBonus();
        $bonus->delete();

        $this->actingAs($this->adminUser())
            ->post(route('bonuses.restore', $bonus->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted('bonuses', ['id' => $bonus->id]);
    }

    public function test_demo_admin_cannot_force_delete_bonus(): void
    {
        $bonus = $this->makeBonus();
        $bonus->delete();

        $this->actingAs($this->demoAdmin())
            ->delete(route('bonuses.force-delete', $bonus->id))
            ->assertForbidden();
    }
}
