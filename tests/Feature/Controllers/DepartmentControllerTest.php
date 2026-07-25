<?php

namespace Tests\Feature\Controllers;

use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for DepartmentController.
 */
class DepartmentControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function test_admin_hr_manager_can_view_departments(): void
    {
        Department::factory()->create();

        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->actingAs($user)->get(route('departments.index'))->assertOk();
        }
    }

    public function test_staff_cannot_view_departments(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('departments.index'))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function test_admin_and_hr_can_create_department(): void
    {
        foreach ([$this->adminUser(), $this->hrUser()] as $user) {
            $this->actingAs($user)->get(route('departments.create'))->assertOk();
        }
    }

    public function test_manager_cannot_create_department(): void
    {
        $this->actingAs($this->managerUser())
            ->get(route('departments.create'))
            ->assertForbidden();
    }

    public function test_admin_can_store_department(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('departments.store'), [
                'name'        => 'Engineering',
                'description' => 'Software engineering team',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('departments', ['name' => 'Engineering']);
    }

    public function test_store_fails_without_name(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('departments.store'), ['description' => 'No name'])
            ->assertSessionHasErrors('name');
    }

    // -----------------------------------------------------------------------
    // Update
    // -----------------------------------------------------------------------

    public function test_admin_can_update_department(): void
    {
        $dept = Department::factory()->create();

        $this->actingAs($this->adminUser())
            ->put(route('departments.update', $dept->id), [
                'name'        => 'Updated Dept',
                'description' => 'New desc',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('departments', ['id' => $dept->id, 'name' => 'Updated Dept']);
    }

    public function test_manager_cannot_update_department(): void
    {
        $dept = Department::factory()->create();

        $this->actingAs($this->managerUser())
            ->put(route('departments.update', $dept->id), ['name' => 'Hacked'])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Delete / Restore
    // -----------------------------------------------------------------------

    public function test_admin_can_delete_department(): void
    {
        $dept = Department::factory()->create();

        $this->actingAs($this->adminUser())
            ->delete(route('departments.destroy', $dept->id))
            ->assertRedirect();

        $this->assertSoftDeleted('departments', ['id' => $dept->id]);
    }

    public function test_hr_can_restore_department(): void
    {
        $dept = Department::factory()->create();
        $dept->delete();

        $this->actingAs($this->hrUser())
            ->post(route('departments.restore', $dept->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted('departments', ['id' => $dept->id]);
    }
}
