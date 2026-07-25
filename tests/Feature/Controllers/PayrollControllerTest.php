<?php

namespace Tests\Feature\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for PayrollController — index views, CRUD, approve, mark-paid, generate.
 */
class PayrollControllerTest extends TestCase
{
    use RefreshDatabase;

    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $dept = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime' => 10_000_000,
            'base_salary_internship' => 2_000_000,
        ]);
        $this->employee = Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $position->id,
            'employee_status' => 'active',
            'employee_type' => 'fulltime',
        ]);
    }

    private function makePayroll(string $status = 'draft', array $overrides = []): Payroll
    {
        return Payroll::factory()->create(array_merge([
            'employee_id' => $this->employee->id,
            'status' => $status,
            'year' => 2025,
            'month' => 6,
        ], $overrides));
    }

    // -----------------------------------------------------------------------
    // Index views
    // -----------------------------------------------------------------------

    public function test_all_roles_can_view_payrolls_index(): void
    {
        foreach ([
            $this->adminUser(),
            $this->hrUser(),
            $this->managerUser(),
            $this->staffUser(),
        ] as $user) {
            $this->actingAs($user)
                ->get(route('payrolls.index'))
                ->assertOk();
        }
    }

    public function test_all_roles_can_view_drafts(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser(), $this->staffUser()] as $user) {
            $this->actingAs($user)->get(route('payrolls.drafts'))->assertOk();
        }
    }

    public function test_all_roles_can_view_approved_payrolls(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser(), $this->staffUser()] as $user) {
            $this->actingAs($user)->get(route('payrolls.approved'))->assertOk();
        }
    }

    public function test_all_roles_can_view_periods(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser(), $this->staffUser()] as $user) {
            $this->actingAs($user)->get(route('payrolls.periods'))->assertOk();
        }
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function test_admin_can_access_create_payroll_form(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('payrolls.create'))
            ->assertOk();
    }

    public function test_staff_cannot_access_create_payroll_form(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('payrolls.create'))
            ->assertForbidden();
    }

    public function test_admin_can_store_payroll(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('payrolls.store'), [
                'employee_id' => $this->employee->id,
                'year' => 2025,
                'month' => 7,
                'pay_date' => '2025-07-25',
                'base_salary' => 10_000_000,
                'bonus' => 0,
                'total_salary' => 10_000_000,
                'status' => 'draft',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $this->employee->id,
            'year' => 2025,
            'month' => 7,
            'status' => 'draft',
        ]);
    }

    public function test_store_fails_with_duplicate_period_for_same_employee(): void
    {
        // Pre-create payroll for month=8
        $this->makePayroll('draft', ['year' => 2025, 'month' => 8]);
        $countBefore = Payroll::count();

        // Attempt to store a duplicate — the controller has no uniqueness validation,
        // so the DB unique constraint fires and Laravel returns a 500 (unhandled exception)
        // or a redirect with errors depending on exception handler configuration.
        // Either way, no new payroll record should exist.
        try {
            $this->actingAs($this->adminUser())
                ->post(route('payrolls.store'), [
                    'employee_id' => $this->employee->id,
                    'year' => 2025,
                    'month' => 8,
                    'pay_date' => '2025-08-25',
                    'base_salary' => 10_000_000,
                    'bonus' => 0,
                    'total_salary' => 10_000_000,
                    'status' => 'draft',
                ]);
        } catch (UniqueConstraintViolationException $e) {
            // Expected — DB constraint prevents duplicate
        }

        $this->assertSame($countBefore, Payroll::count());
    }

    // -----------------------------------------------------------------------
    // Show
    // -----------------------------------------------------------------------

    public function test_admin_can_view_payroll_detail(): void
    {
        $payroll = $this->makePayroll();

        $this->actingAs($this->adminUser())
            ->get(route('payrolls.show', $payroll->id))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // Approve workflow
    // -----------------------------------------------------------------------

    public function test_admin_can_approve_draft_payroll(): void
    {
        $payroll = $this->makePayroll('draft');

        $this->actingAs($this->adminUser())
            ->post(route('payrolls.approve', $payroll->id))
            ->assertRedirect();

        $this->assertDatabaseHas('payrolls', ['id' => $payroll->id, 'status' => 'approved']);
    }

    public function test_hr_can_approve_draft_payroll(): void
    {
        $payroll = $this->makePayroll('draft');

        $this->actingAs($this->hrUser())
            ->post(route('payrolls.approve', $payroll->id))
            ->assertRedirect();

        $this->assertDatabaseHas('payrolls', ['id' => $payroll->id, 'status' => 'approved']);
    }

    public function test_manager_cannot_approve_payroll_via_route(): void
    {
        $payroll = $this->makePayroll('draft');

        $this->actingAs($this->managerUser())
            ->post(route('payrolls.approve', $payroll->id))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Mark Paid workflow
    // -----------------------------------------------------------------------

    public function test_admin_can_mark_payroll_as_paid(): void
    {
        $payroll = $this->makePayroll('approved');

        $this->actingAs($this->adminUser())
            ->post(route('payrolls.mark-paid', $payroll->id))
            ->assertRedirect();

        $this->assertDatabaseHas('payrolls', ['id' => $payroll->id, 'status' => 'paid']);
    }

    public function test_staff_cannot_mark_payroll_as_paid(): void
    {
        $payroll = $this->makePayroll('approved');

        $this->actingAs($this->staffUser())
            ->post(route('payrolls.mark-paid', $payroll->id))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Bulk approve / mark-paid
    // -----------------------------------------------------------------------

    public function test_admin_can_bulk_approve_draft_payrolls(): void
    {
        $p1 = $this->makePayroll('draft', ['month' => 1]);
        $p2 = Payroll::factory()->create([
            'employee_id' => $this->employee->id,
            'status' => 'draft',
            'year' => 2025,
            'month' => 2,
        ]);

        $this->actingAs($this->adminUser())
            ->post(route('payrolls.drafts.approve-all'), ['ids' => [$p1->id, $p2->id]])
            ->assertRedirect();

        $this->assertDatabaseHas('payrolls', ['id' => $p1->id, 'status' => 'approved']);
        $this->assertDatabaseHas('payrolls', ['id' => $p2->id, 'status' => 'approved']);
    }

    public function test_staff_cannot_bulk_approve(): void
    {
        $payroll = $this->makePayroll('draft');

        $this->actingAs($this->staffUser())
            ->post(route('payrolls.drafts.approve-all'), ['ids' => [$payroll->id]])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Generate (bulk)
    // -----------------------------------------------------------------------

    public function test_admin_can_access_generate_form(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('payrolls.generate'))
            ->assertOk();
    }

    public function test_staff_cannot_access_generate_form(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('payrolls.generate'))
            ->assertForbidden();
    }

    public function test_admin_can_generate_bulk_payroll(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('payrolls.generate.bulk'), [
                'year' => 2025,
                'month' => 9,
                'pay_date' => '2025-09-25',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $this->employee->id,
            'year' => 2025,
            'month' => 9,
            'status' => 'draft',
        ]);
    }

    // -----------------------------------------------------------------------
    // Soft delete / Restore
    // -----------------------------------------------------------------------

    public function test_admin_can_soft_delete_payroll(): void
    {
        $payroll = $this->makePayroll();

        $this->actingAs($this->adminUser())
            ->delete(route('payrolls.destroy', $payroll->id))
            ->assertRedirect();

        $this->assertSoftDeleted('payrolls', ['id' => $payroll->id]);
    }

    public function test_admin_can_restore_payroll(): void
    {
        $payroll = $this->makePayroll();
        $payroll->delete();

        $this->actingAs($this->adminUser())
            ->post(route('payrolls.restore', $payroll->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted('payrolls', ['id' => $payroll->id]);
    }

    // -----------------------------------------------------------------------
    // Export
    // -----------------------------------------------------------------------

    public function test_admin_can_export_single_payroll(): void
    {
        $payroll = $this->makePayroll('paid');

        $response = $this->actingAs($this->adminUser())
            ->get(route('payrolls.export', $payroll->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_export_approved_payrolls(): void
    {
        $this->makePayroll('approved');

        $response = $this->actingAs($this->adminUser())
            ->get(route('payrolls.approved.export', ['year' => 2025, 'month' => 6]));

        $response->assertOk();
    }
}
