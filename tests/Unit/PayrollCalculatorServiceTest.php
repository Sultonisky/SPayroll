<?php

namespace Tests\Unit;

use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Services\PayrollCalculatorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for PayrollCalculatorService.
 * Uses DB to set up real Employee/Position/Bonus records.
 */
class PayrollCalculatorServiceTest extends TestCase
{
    use RefreshDatabase;

    private PayrollCalculatorService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PayrollCalculatorService;
    }

    private function createEmployee(string $type, float $fulltime, float $internship): Employee
    {
        $dept = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime' => $fulltime,
            'base_salary_internship' => $internship,
        ]);

        return Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $position->id,
            'employee_status' => 'active',
            'employee_type' => $type,
        ]);
    }

    // -----------------------------------------------------------------------
    // calculate() — single employee
    // -----------------------------------------------------------------------

    public function test_calculate_returns_correct_shape(): void
    {
        $employee = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertArrayHasKey('employee_id', $result);
        $this->assertArrayHasKey('year', $result);
        $this->assertArrayHasKey('month', $result);
        $this->assertArrayHasKey('base_salary', $result);
        $this->assertArrayHasKey('bonus', $result);
        $this->assertArrayHasKey('total_salary', $result);
    }

    public function test_fulltime_base_salary_uses_position_fulltime_rate(): void
    {
        $employee = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertEquals(10_000_000, $result['base_salary']);
    }

    public function test_internship_base_salary_uses_position_internship_rate(): void
    {
        $employee = $this->createEmployee('internship', 10_000_000, 2_000_000);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertEquals(2_000_000, $result['base_salary']);
    }

    public function test_bonus_zero_when_no_approved_bonuses(): void
    {
        $employee = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertEquals(0.0, $result['bonus']);
    }

    public function test_bonus_sums_approved_bonuses_for_period(): void
    {
        $employee = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        Bonus::factory()->approved()->create([
            'employee_id' => $employee->id,
            'year' => 2025,
            'month' => 6,
            'amount' => 1_000_000,
        ]);
        Bonus::factory()->approved()->create([
            'employee_id' => $employee->id,
            'year' => 2025,
            'month' => 6,
            'amount' => 500_000,
        ]);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertEquals(1_500_000, $result['bonus']);
    }

    public function test_pending_bonus_not_included_in_calculation(): void
    {
        $employee = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        Bonus::factory()->pending()->create([
            'employee_id' => $employee->id,
            'year' => 2025,
            'month' => 6,
            'amount' => 999_000,
        ]);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertEquals(0.0, $result['bonus']);
    }

    public function test_bonus_from_different_period_not_included(): void
    {
        $employee = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        Bonus::factory()->approved()->create([
            'employee_id' => $employee->id,
            'year' => 2025,
            'month' => 5, // different month
            'amount' => 2_000_000,
        ]);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertEquals(0.0, $result['bonus']);
    }

    public function test_total_salary_equals_base_plus_bonus(): void
    {
        $employee = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        Bonus::factory()->approved()->create([
            'employee_id' => $employee->id,
            'year' => 2025,
            'month' => 6,
            'amount' => 1_000_000,
        ]);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertEquals(11_000_000, $result['total_salary']);
    }

    public function test_base_salary_zero_when_no_position(): void
    {
        $dept = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime' => null,
            'base_salary_internship' => null,
        ]);
        $employee = Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $position->id,
            'employee_status' => 'active',
            'employee_type' => 'fulltime',
        ]);

        $result = $this->service->calculate($employee, 2025, 6);

        $this->assertEquals(0.0, $result['base_salary']);
    }

    // -----------------------------------------------------------------------
    // generateBulk()
    // -----------------------------------------------------------------------

    public function test_generate_bulk_creates_draft_payrolls_for_active_employees(): void
    {
        $active = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        // Create a second employee explicitly as inactive — should be skipped
        $dept = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime' => 5_000_000,
            'base_salary_internship' => 1_500_000,
        ]);
        Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $position->id,
            'employee_status' => 'inactive',
            'employee_type' => 'fulltime',
        ]);

        $result = $this->service->generateBulk(2025, 7, '2025-07-25');

        $this->assertSame(1, $result['created']);
        $this->assertSame(0, $result['skipped']);
        $this->assertDatabaseHas('payrolls', [
            'employee_id' => $active->id,
            'year' => 2025,
            'month' => 7,
            'status' => 'draft',
        ]);
    }

    public function test_generate_bulk_skips_existing_payroll_for_same_period(): void
    {
        $employee = $this->createEmployee('fulltime', 10_000_000, 2_000_000);

        // Pre-existing payroll for same period
        Payroll::factory()->create([
            'employee_id' => $employee->id,
            'year' => 2025,
            'month' => 7,
        ]);

        $result = $this->service->generateBulk(2025, 7, '2025-07-25');

        $this->assertSame(0, $result['created']);
        $this->assertSame(1, $result['skipped']);
    }

    public function test_generate_bulk_returns_zero_when_no_active_employees(): void
    {
        $result = $this->service->generateBulk(2025, 7, '2025-07-25');

        $this->assertSame(0, $result['created']);
        $this->assertSame(0, $result['skipped']);
    }
}
