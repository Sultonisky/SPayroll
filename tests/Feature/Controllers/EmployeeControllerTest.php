<?php

namespace Tests\Feature\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for EmployeeController — CRUD, filters, export, soft-delete, RBAC.
 */
class EmployeeControllerTest extends TestCase
{
    use RefreshDatabase;

    private Department $dept;
    private Position   $position;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dept     = Department::factory()->create();
        $this->position = Position::factory()->create([
            'base_salary_fulltime'   => 8_000_000,
            'base_salary_internship' => 2_000_000,
        ]);
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'department_id'       => $this->dept->id,
            'position_id'         => $this->position->id,
            'nik'                 => '3171234567890001',
            'name'                => 'Test Employee',
            'email'               => 'emp@test.com',
            'phone'               => '081234567890',
            'address'             => 'Jl. Test No. 1',
            'join_date'           => '2024-01-01',
            'birth_date'          => '1995-06-15',
            'employee_status'     => 'active',
            'employee_type'       => 'fulltime',
            'bank_name'           => 'BCA',
            'bank_account_number' => '1234567890',
            'gender'              => 'laki-laki',
        ], $overrides);
    }

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function test_all_roles_can_view_employee_list(): void
    {
        Employee::factory()->count(2)->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);

        foreach ([
            $this->adminUser(),
            $this->hrUser(),
            $this->managerUser(),
            $this->staffUser(),
        ] as $user) {
            $this->actingAs($user)
                ->get(route('employees.index'))
                ->assertOk();
        }
    }

    public function test_index_can_filter_by_status(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('employees.index', ['status' => 'active']))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function test_admin_hr_manager_can_access_create_form(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->actingAs($user)
                ->get(route('employees.create'))
                ->assertOk();
        }
    }

    public function test_staff_cannot_access_create_form(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('employees.create'))
            ->assertForbidden();
    }

    public function test_admin_can_store_employee(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('employees.store'), $this->validPayload())
            ->assertRedirect();

        $this->assertDatabaseHas('employees', ['email' => 'emp@test.com']);
    }

    public function test_employee_code_auto_generated_on_store(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('employees.store'), $this->validPayload());

        $employee = Employee::where('email', 'emp@test.com')->first();
        $this->assertNotNull($employee->employee_code);
    }

    public function test_store_fails_without_required_fields(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('employees.store'), [])
            ->assertSessionHasErrors(['nik', 'name', 'email']);
    }

    public function test_store_fails_with_duplicate_nik(): void
    {
        Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
            'nik'           => '3171234567890001',
        ]);

        $this->actingAs($this->adminUser())
            ->post(route('employees.store'), $this->validPayload(['nik' => '3171234567890001']))
            ->assertSessionHasErrors('nik');
    }

    public function test_store_with_other_bank_uses_bank_name_other(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('employees.store'), $this->validPayload([
                'bank_name'       => 'Other',
                'bank_name_other' => 'Bank Custom',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('employees', ['bank_name' => 'Bank Custom']);
    }

    // -----------------------------------------------------------------------
    // Show
    // -----------------------------------------------------------------------

    public function test_all_roles_can_view_employee_detail(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);

        foreach ([
            $this->adminUser(),
            $this->hrUser(),
            $this->managerUser(),
            $this->staffUser(),
        ] as $user) {
            $this->actingAs($user)
                ->get(route('employees.show', $employee->id))
                ->assertOk();
        }
    }

    // -----------------------------------------------------------------------
    // Update
    // -----------------------------------------------------------------------

    public function test_admin_can_update_employee(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);

        $this->actingAs($this->adminUser())
            ->put(route('employees.update', $employee->id), $this->validPayload([
                'nik'   => $employee->nik,
                'email' => $employee->email,
                'name'  => 'Updated Name',
            ]))
            ->assertRedirect();

        $this->assertDatabaseHas('employees', ['id' => $employee->id, 'name' => 'Updated Name']);
    }

    public function test_staff_cannot_update_employee(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);

        $this->actingAs($this->staffUser())
            ->put(route('employees.update', $employee->id), $this->validPayload())
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Destroy / Restore / Force Delete
    // -----------------------------------------------------------------------

    public function test_admin_can_soft_delete_employee(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);

        $this->actingAs($this->adminUser())
            ->delete(route('employees.destroy', $employee->id))
            ->assertRedirect();

        $this->assertSoftDeleted('employees', ['id' => $employee->id]);
    }

    public function test_manager_cannot_delete_employee(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);

        $this->actingAs($this->managerUser())
            ->delete(route('employees.destroy', $employee->id))
            ->assertForbidden();
    }

    public function test_admin_can_restore_soft_deleted_employee(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);
        $employee->delete();

        $this->actingAs($this->adminUser())
            ->post(route('employees.restore', $employee->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted('employees', ['id' => $employee->id]);
    }

    public function test_demo_admin_cannot_force_delete_employee(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);
        $employee->delete();

        $this->actingAs($this->demoAdmin())
            ->delete(route('employees.force-delete', $employee->id))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Export CSV
    // -----------------------------------------------------------------------

    public function test_admin_can_export_employee_csv(): void
    {
        $employee = Employee::factory()->create([
            'department_id' => $this->dept->id,
            'position_id'   => $this->position->id,
        ]);

        $response = $this->actingAs($this->adminUser())
            ->get(route('employees.export', $employee->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }
}
