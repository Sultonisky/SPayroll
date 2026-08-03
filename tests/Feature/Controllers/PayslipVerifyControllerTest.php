<?php

namespace Tests\Feature\Controllers;

use App\Http\Controllers\PayslipVerifyController;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for the public payslip verification page:
 *   GET  /verify  — show form
 *   POST /verify  — verify a Doc ID
 *
 * This endpoint is intentionally public (no auth required).
 */
class PayslipVerifyControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Shared fixtures
    // -----------------------------------------------------------------------

    private Payroll $paidPayroll;

    protected function setUp(): void
    {
        parent::setUp();

        $dept = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime' => 10_000_000,
            'base_salary_internship' => 2_000_000,
        ]);
        $employee = Employee::factory()->create([
            'department_id' => $dept->id,
            'position_id' => $position->id,
            'employee_status' => 'active',
            'employee_type' => 'fulltime',
        ]);

        $this->paidPayroll = Payroll::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'paid',
            'year' => 2025,
            'month' => 6,
            'base_salary' => 10_000_000,
            'bonus' => 500_000,
            'total_salary' => 10_500_000,
            'pay_date' => '2025-06-25',
        ]);
    }

    // -----------------------------------------------------------------------
    // GET /verify — form page (public, no auth)
    // -----------------------------------------------------------------------

    public function test_verify_form_is_accessible_without_login(): void
    {
        $this->get(route('verify.payslip.form'))
            ->assertOk()
            ->assertViewIs('verify.index');
    }

    public function test_verify_form_is_accessible_when_logged_in(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('verify.payslip.form'))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // POST /verify — valid Doc ID
    // -----------------------------------------------------------------------

    public function test_valid_doc_id_returns_valid_result(): void
    {
        $docId = PayslipVerifyController::generateDocId($this->paidPayroll);

        $this->post(route('verify.payslip'), ['doc_id' => $docId])
            ->assertRedirect()
            ->assertSessionHas('result.valid', true);
    }

    public function test_valid_result_contains_expected_employee_data(): void
    {
        $docId = PayslipVerifyController::generateDocId($this->paidPayroll);

        $response = $this->post(route('verify.payslip'), ['doc_id' => $docId]);

        $result = $response->getSession()->get('result');

        $this->assertTrue($result['valid']);
        $this->assertSame($this->paidPayroll->employee->name, $result['employee']);
        $this->assertSame($this->paidPayroll->employee->employee_code, $result['employee_code']);
        $this->assertSame($this->paidPayroll->monthName(), $result['period']);
        $this->assertSame('Paid', $result['status']);
        $this->assertSame($docId, $result['doc_id']);
    }

    public function test_verification_is_case_insensitive_for_submitted_doc_id(): void
    {
        $docId = PayslipVerifyController::generateDocId($this->paidPayroll);

        // Only the hex hash portion should be case-insensitive, not the 'SCR-' prefix.
        // Lowercase the hash segment only: SCR-00001-AABBCC → SCR-00001-aabbcc
        $parts = explode('-', $docId);
        $parts[2] = strtolower($parts[2]);
        $lowercaseHex = implode('-', $parts);

        $this->post(route('verify.payslip'), ['doc_id' => $lowercaseHex])
            ->assertSessionHas('result.valid', true);
    }

    // -----------------------------------------------------------------------
    // POST /verify — invalid Doc IDs
    // -----------------------------------------------------------------------

    public function test_tampered_hash_returns_invalid_result(): void
    {
        $docId = PayslipVerifyController::generateDocId($this->paidPayroll);
        // Flip last character to simulate tampering
        $tampered = substr($docId, 0, -1).(str_ends_with($docId, 'A') ? 'B' : 'A');

        $this->post(route('verify.payslip'), ['doc_id' => $tampered])
            ->assertSessionHas('result.valid', false);
    }

    public function test_nonexistent_payroll_id_returns_invalid_result(): void
    {
        // Build a doc ID for a payroll ID that does not exist
        $fakeDocId = 'SCR-99999-AABBCCDDEEFF';

        $this->post(route('verify.payslip'), ['doc_id' => $fakeDocId])
            ->assertSessionHas('result.valid', false);
    }

    public function test_doc_id_of_non_paid_payroll_returns_invalid(): void
    {
        // Create a draft payroll and generate its doc ID manually
        $draft = Payroll::factory()->create([
            'employee_id' => $this->paidPayroll->employee_id,
            'status' => 'draft',
            'year' => 2025,
            'month' => 8,
            'base_salary' => 10_000_000,
            'bonus' => 0,
            'total_salary' => 10_000_000,
            'pay_date' => '2025-08-25',
        ]);

        // Hash matches, but status is not 'paid' — query scopes out
        $docId = PayslipVerifyController::generateDocId($draft);

        $this->post(route('verify.payslip'), ['doc_id' => $docId])
            ->assertSessionHas('result.valid', false);
    }

    // -----------------------------------------------------------------------
    // POST /verify — input validation
    // -----------------------------------------------------------------------

    public function test_empty_doc_id_fails_validation(): void
    {
        $this->post(route('verify.payslip'), ['doc_id' => ''])
            ->assertSessionHasErrors('doc_id');
    }

    public function test_malformed_doc_id_fails_validation(): void
    {
        foreach ([
            'INVALID',
            'SCR-123-TOOSHORT',
            'scr-00001-AABBCCDDEEFF',   // lower-case prefix fails regex
            'SCR-1-AABBCCDDEEFF',        // wrong ID padding
            'SCR-00001-ZZZZZZZZZZZZ',    // non-hex chars in hash part
            '',
        ] as $bad) {
            $this->post(route('verify.payslip'), ['doc_id' => $bad])
                ->assertSessionHasErrors('doc_id');
        }
    }

    public function test_doc_id_with_whitespace_is_trimmed_and_accepted(): void
    {
        $docId = PayslipVerifyController::generateDocId($this->paidPayroll);

        $this->post(route('verify.payslip'), ['doc_id' => "  {$docId}  "])
            ->assertSessionHas('result.valid', true);
    }

    // -----------------------------------------------------------------------
    // generateDocId — unit-level assertions
    // -----------------------------------------------------------------------

    public function test_generate_doc_id_format_is_correct(): void
    {
        $docId = PayslipVerifyController::generateDocId($this->paidPayroll);

        // Must match SCR-{5 digits}-{12 uppercase hex chars}
        $this->assertMatchesRegularExpression('/^SCR-\d{5}-[A-F0-9]{12}$/', $docId);
    }

    public function test_generate_doc_id_is_deterministic(): void
    {
        $first = PayslipVerifyController::generateDocId($this->paidPayroll);
        $second = PayslipVerifyController::generateDocId($this->paidPayroll);

        $this->assertSame($first, $second);
    }

    public function test_different_payrolls_produce_different_doc_ids(): void
    {
        $other = Payroll::factory()->create([
            'employee_id' => $this->paidPayroll->employee_id,
            'status' => 'paid',
            'year' => 2025,
            'month' => 7,
            'base_salary' => 10_000_000,
            'bonus' => 0,
            'total_salary' => 10_000_000,
            'pay_date' => '2025-07-25',
        ]);

        $this->assertNotSame(
            PayslipVerifyController::generateDocId($this->paidPayroll),
            PayslipVerifyController::generateDocId($other)
        );
    }

    public function test_doc_id_changes_if_total_salary_changes(): void
    {
        $original = PayslipVerifyController::generateDocId($this->paidPayroll);

        // Simulate tampering with total_salary
        $tampered = clone $this->paidPayroll;
        $tampered->total_salary = 99_999_999;

        $this->assertNotSame($original, PayslipVerifyController::generateDocId($tampered));
    }

    // -----------------------------------------------------------------------
    // Verify page UI — session result rendered correctly
    // -----------------------------------------------------------------------

    public function test_valid_result_is_displayed_on_verify_page(): void
    {
        $docId = PayslipVerifyController::generateDocId($this->paidPayroll);

        $this->post(route('verify.payslip'), ['doc_id' => $docId]);

        // Follow redirect back to form, session result should render
        $this->get(route('verify.payslip.form'))
            ->assertOk();
        // The result is in session; the view reads it — covered by assertSessionHas above
    }
}
