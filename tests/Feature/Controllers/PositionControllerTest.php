<?php

namespace Tests\Feature\Controllers;

use App\Models\Position;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for PositionController — including salary validation rules.
 */
class PositionControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function test_admin_hr_manager_can_view_positions(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->actingAs($user)->get(route('positions.index'))->assertOk();
        }
    }

    public function test_staff_cannot_view_positions(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('positions.index'))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function test_admin_can_store_position(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('positions.store'), [
                'name' => 'Software Engineer',
                'description' => 'Develops software',
                'base_salary_fulltime' => 10_000_000,
                'base_salary_internship' => 2_000_000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('positions', ['name' => 'Software Engineer']);
    }

    public function test_store_fails_when_internship_salary_exceeds_fulltime(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('positions.store'), [
                'name' => 'Test Role',
                'base_salary_fulltime' => 5_000_000,
                'base_salary_internship' => 9_000_000, // must be <= fulltime
            ])
            ->assertSessionHasErrors('base_salary_internship');
    }

    public function test_manager_cannot_create_position(): void
    {
        $this->actingAs($this->managerUser())
            ->post(route('positions.store'), [
                'name' => 'Test Role',
                'base_salary_fulltime' => 5_000_000,
                'base_salary_internship' => 2_000_000,
            ])
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Update
    // -----------------------------------------------------------------------

    public function test_admin_can_update_position(): void
    {
        $position = Position::factory()->create([
            'base_salary_fulltime' => 10_000_000,
            'base_salary_internship' => 2_000_000,
        ]);

        $this->actingAs($this->adminUser())
            ->put(route('positions.update', $position->id), [
                'name' => 'Senior Engineer',
                'base_salary_fulltime' => 15_000_000,
                'base_salary_internship' => 3_000_000,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('positions', ['id' => $position->id, 'name' => 'Senior Engineer']);
    }

    // -----------------------------------------------------------------------
    // Delete / Restore
    // -----------------------------------------------------------------------

    public function test_admin_can_delete_and_restore_position(): void
    {
        $position = Position::factory()->create([
            'base_salary_fulltime' => 10_000_000,
            'base_salary_internship' => 2_000_000,
        ]);

        $this->actingAs($this->adminUser())
            ->delete(route('positions.destroy', $position->id))
            ->assertRedirect();

        $this->assertSoftDeleted('positions', ['id' => $position->id]);

        $this->actingAs($this->adminUser())
            ->post(route('positions.restore', $position->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted('positions', ['id' => $position->id]);
    }
}
