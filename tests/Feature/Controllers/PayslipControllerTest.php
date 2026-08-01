<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\PayslipVerifyController;
use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for payslip routes:
 *   GET  /payslips           (payslipIndex)
 *   GET  /payslips/{id}      (payslipShow)
 *   GET  /payslips/{id}/download  (payslipDownload)
 *
 * Access matrix (best-practice, least-privilege):
 *   admin / HR / finance  → all employees
 *   staff                 → own payslip only
 *   manager               → denied (403)
 */
class PayslipControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Shared fixtures
    // -----------------------------------------------------------------------

    private Employee $employee;
    private Payroll  $paidPayroll;

    protected function setUp(): void
    {
        parent::setUp();

        $dept     = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime'   => 10_000_000,
            'base_salary_internship' => 2_000_000,
        ]);

        $this->employee = Employee::factory()->create([
            'department_id'   => $dept->id,
            'position_id'     => $position->id,
            'employee_status' => 'active',
            'employee_type'   => 'fulltime',
        ]);

        $this->paidPayroll = Payroll::factory()->create([
            'employee_id'  => $this->employee->id,
            'status'       => 'paid',
            'year'         => 2025,
            'month'        => 6,
            'base_salary'  => 10_000_000,
            'bonus'        => 0,
            'total_salary' => 10_000_000,
            'pay_date'     => '2025-06-25',
        ]);
    }

    /** Create a staff user linked to $this->employee. */
    private function staffWithEmployee(): User
    {
        $user = $this->staffUser();
        $this->employee->update(['user_id' => $user->id]);
        return $user;
    }

    /** Create a finance user. */
    private function financeUser(): \App\Models\User
    {
        return \App\Models\User::factory()->finance()->create();
    }

    // -----------------------------------------------------------------------
    // GET /payslips — index
    // -----------------------------------------------------------------------

    public function test_admin_can_access_payslip_index(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip'))
            ->assertOk();
    }

    public function test_hr_can_access_payslip_index(): void
    {
        $this->actingAs($this->hrUser())
            ->get(route('payrolls.payslip'))
            ->assertOk();
    }

    public function test_finance_can_access_payslip_index(): void
    {
        $this->actingAs($this->financeUser())
            ->get(route('payrolls.payslip'))
            ->assertOk();
    }

    public function test_staff_can_access_payslip_index(): void
    {
        $this->actingAs($this->staffWithEmployee())
            ->get(route('payrolls.payslip'))
            ->assertOk();
    }

    public function test_manager_cannot_access_payslip_index(): void
    {
        $this->actingAs($this->managerUser())
            ->get(route('payrolls.payslip'))
            ->assertForbidden();
    }

    public function test_unauthenticated_user_is_redirected_from_payslip_index(): void
    {
        $this->get(route('payrolls.payslip'))
            ->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // Index — staff sees only own payslips
    // -----------------------------------------------------------------------

    public function test_staff_only_sees_own_payslips_in_index(): void
    {
        // Another employee + their paid payroll
        $otherEmployee = Employee::factory()->create([
            'department_id'   => $this->employee->department_id,
            'position_id'     => $this->employee->position_id,
            'employee_status' => 'active',
        ]);
        Payroll::factory()->create([
            'employee_id'  => $otherEmployee->id,
            'status'       => 'paid',
            'year'         => 2025,
            'month'        => 7,
            'base_salary'  => 8_000_000,
            'bonus'        => 0,
            'total_salary' => 8_000_000,
        ]);

        $staff = $this->staffWithEmployee();

        $response = $this->actingAs($staff)
            ->get(route('payrolls.payslip'))
            ->assertOk();

        // Staff's own payslip must be present
        $response->assertSee($this->employee->name);

        // Other employee's name must NOT appear
        $response->assertDontSee($otherEmployee->name);
    }

    public function test_admin_sees_all_payslips_in_index(): void
    {
        $otherEmployee = Employee::factory()->create([
            'department_id'   => $this->employee->department_id,
            'position_id'     => $this->employee->position_id,
            'employee_status' => 'active',
        ]);
        Payroll::factory()->create([
            'employee_id'  => $otherEmployee->id,
            'status'       => 'paid',
            'year'         => 2025,
            'month'        => 7,
            'base_salary'  => 8_000_000,
            'bonus'        => 0,
            'total_salary' => 8_000_000,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip'))
            ->assertOk();

        $response->assertSee($this->employee->name);
        $response->assertSee($otherEmployee->name);
    }

    // -----------------------------------------------------------------------
    // Index — only paid payrolls appear
    // -----------------------------------------------------------------------

    public function test_draft_and_approved_payrolls_do_not_appear_in_payslip_index(): void
    {
        $draftPayroll = Payroll::factory()->create([
            'employee_id' => $this->employee->id,
            'status'      => 'draft',
            'year'        => 2025,
            'month'       => 8,
        ]);
        $approvedPayroll = Payroll::factory()->create([
            'employee_id' => $this->employee->id,
            'status'      => 'approved',
            'year'        => 2025,
            'month'       => 9,
        ]);

        $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip'))
            ->assertOk()
            ->assertViewHas('payrolls', function ($payrolls) use ($draftPayroll, $approvedPayroll) {
                return $payrolls->doesntContain('id', $draftPayroll->id)
                    && $payrolls->doesntContain('id', $approvedPayroll->id);
            });
    }

    // -----------------------------------------------------------------------
    // GET /payslips/{id} — show
    // -----------------------------------------------------------------------

    public function test_admin_can_view_payslip_detail(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip.show', $this->paidPayroll->id))
            ->assertOk();
    }

    public function test_hr_can_view_payslip_detail(): void
    {
        $this->actingAs($this->hrUser())
            ->get(route('payrolls.payslip.show', $this->paidPayroll->id))
            ->assertOk();
    }

    public function test_finance_can_view_payslip_detail(): void
    {
        $this->actingAs($this->financeUser())
            ->get(route('payrolls.payslip.show', $this->paidPayroll->id))
            ->assertOk();
    }

    public function test_staff_can_view_own_payslip_detail(): void
    {
        $this->actingAs($this->staffWithEmployee())
            ->get(route('payrolls.payslip.show', $this->paidPayroll->id))
            ->assertOk();
    }

    public function test_staff_cannot_view_another_employees_payslip(): void
    {
        // Staff linked to a DIFFERENT employee
        $otherEmployee = Employee::factory()->create([
            'department_id'   => $this->employee->department_id,
            'position_id'     => $this->employee->position_id,
            'employee_status' => 'active',
        ]);
        $otherStaff = $this->staffUser();
        $otherEmployee->update(['user_id' => $otherStaff->id]);

        // Try to access $this->paidPayroll which belongs to $this->employee, not $otherStaff
        $this->actingAs($otherStaff)
            ->get(route('payrolls.payslip.show', $this->paidPayroll->id))
            ->assertForbidden();
    }

    public function test_manager_cannot_view_payslip_detail(): void
    {
        $this->actingAs($this->managerUser())
            ->get(route('payrolls.payslip.show', $this->paidPayroll->id))
            ->assertForbidden();
    }

    public function test_payslip_show_returns_404_for_non_paid_payroll(): void
    {
        $draft = Payroll::factory()->create([
            'employee_id' => $this->employee->id,
            'status'      => 'draft',
            'year'        => 2025,
            'month'       => 10,
        ]);

        $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip.show', $draft->id))
            ->assertNotFound();
    }

    // -----------------------------------------------------------------------
    // GET /payslips/{id}/download — PDF download
    // -----------------------------------------------------------------------

    public function test_admin_can_download_payslip_pdf(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip.download', $this->paidPayroll->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_hr_can_download_payslip_pdf(): void
    {
        $response = $this->actingAs($this->hrUser())
            ->get(route('payrolls.payslip.download', $this->paidPayroll->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_finance_can_download_payslip_pdf(): void
    {
        $response = $this->actingAs($this->financeUser())
            ->get(route('payrolls.payslip.download', $this->paidPayroll->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_staff_can_download_own_payslip_pdf(): void
    {
        $response = $this->actingAs($this->staffWithEmployee())
            ->get(route('payrolls.payslip.download', $this->paidPayroll->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_staff_cannot_download_another_employees_payslip_pdf(): void
    {
        $otherEmployee = Employee::factory()->create([
            'department_id'   => $this->employee->department_id,
            'position_id'     => $this->employee->position_id,
            'employee_status' => 'active',
        ]);
        $otherStaff = $this->staffUser();
        $otherEmployee->update(['user_id' => $otherStaff->id]);

        $this->actingAs($otherStaff)
            ->get(route('payrolls.payslip.download', $this->paidPayroll->id))
            ->assertForbidden();
    }

    public function test_manager_cannot_download_payslip_pdf(): void
    {
        $this->actingAs($this->managerUser())
            ->get(route('payrolls.payslip.download', $this->paidPayroll->id))
            ->assertForbidden();
    }

    public function test_pdf_filename_contains_employee_code_and_period(): void
    {
        $response = $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip.download', $this->paidPayroll->id));

        $response->assertOk();

        $disposition = $response->headers->get('Content-Disposition');
        $expectedCode   = $this->employee->employee_code;
        $expectedPeriod = '202506';

        $this->assertStringContainsString($expectedCode,   $disposition);
        $this->assertStringContainsString($expectedPeriod, $disposition);
    }

    // -----------------------------------------------------------------------
    // Payslip index filter — year / month
    // -----------------------------------------------------------------------

    public function test_payslip_index_filters_by_year_and_month(): void
    {
        // Extra paid payroll in a different period
        Payroll::factory()->create([
            'employee_id'  => $this->employee->id,
            'status'       => 'paid',
            'year'         => 2024,
            'month'        => 1,
            'base_salary'  => 10_000_000,
            'bonus'        => 0,
            'total_salary' => 10_000_000,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip', ['year' => 2025, 'month' => 6]))
            ->assertOk();

        $response->assertViewHas('payrolls', function ($payrolls) {
            return $payrolls->every(fn ($p) => $p->year === 2025 && $p->month === 6);
        });
    }

    // -----------------------------------------------------------------------
    // Payslip with bonus breakdown visible
    // -----------------------------------------------------------------------

    public function test_payslip_show_displays_bonus_breakdown(): void
    {
        Bonus::factory()->create([
            'employee_id' => $this->employee->id,
            'year'        => 2025,
            'month'       => 6,
            'type'        => 'performance',
            'description' => 'Q2 bonus',
            'amount'      => 1_500_000,
            'status'      => 'approved',
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('payrolls.payslip.show', $this->paidPayroll->id))
            ->assertOk();

        // View renders ucfirst($bonus->type) and $bonus->description
        $response->assertSee('Performance');
        $response->assertSee('Q2 bonus');
    }
}
