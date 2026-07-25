<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Unit tests for EmployeeObserver — focuses on the employee_code auto-generation.
 */
class EmployeeObserverTest extends TestCase
{
    use RefreshDatabase;

    private function baseAttributes(): array
    {
        $dept     = Department::factory()->create();
        $position = Position::factory()->create([
            'base_salary_fulltime'   => 5_000_000,
            'base_salary_internship' => 2_000_000,
        ]);

        return [
            'department_id'       => $dept->id,
            'position_id'         => $position->id,
            'nik'                 => '3171234567890001',
            'name'                => 'Test Employee',
            'email'               => 'employee@test.com',
            'phone'               => '081234567890',
            'join_date'           => '2024-01-01',
            'employee_status'     => 'active',
            'employee_type'       => 'fulltime',
            'bank_name'           => 'BCA',
            'bank_account_number' => '1234567890',
            'gender'              => 'laki-laki',
        ];
    }

    public function test_employee_code_auto_generated_on_create(): void
    {
        $employee = Employee::create($this->baseAttributes());

        $this->assertNotNull($employee->employee_code);
        $this->assertMatchesRegularExpression('/^\d{3}$/', $employee->employee_code);
    }

    public function test_first_employee_gets_code_001(): void
    {
        $employee = Employee::create($this->baseAttributes());

        $this->assertSame('001', $employee->employee_code);
    }

    public function test_second_employee_gets_sequential_code(): void
    {
        $attrs = $this->baseAttributes();
        Employee::create($attrs);

        $attrs['nik']   = '3171234567890002';
        $attrs['email'] = 'employee2@test.com';
        $attrs['phone'] = '081234567891';
        $second = Employee::create($attrs);

        $this->assertSame('002', $second->employee_code);
    }

    public function test_existing_code_not_overwritten_on_create(): void
    {
        $attrs = $this->baseAttributes();
        $attrs['employee_code'] = '999';
        $employee = Employee::create($attrs);

        $this->assertSame('999', $employee->employee_code);
    }

    public function test_soft_deleted_employees_counted_in_sequence(): void
    {
        // Create and soft-delete first employee
        $first = Employee::create($this->baseAttributes());
        $first->delete();

        $attrs = $this->baseAttributes();
        $attrs['nik']   = '3171234567890002';
        $attrs['email'] = 'employee2@test.com';
        $attrs['phone'] = '081234567891';

        $second = Employee::create($attrs);

        // Should be 002 because 001 still occupies the code (withTrashed)
        $this->assertSame('002', $second->employee_code);
    }
}
