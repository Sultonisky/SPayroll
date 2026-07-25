<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for UserController — CRUD, soft-delete, restore, force-delete.
 */
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Index
    // -----------------------------------------------------------------------

    public function test_admin_can_list_users(): void
    {
        User::factory()->count(3)->create();

        $this->actingAs($this->adminUser())
            ->get(route('users.index'))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // Create / Store
    // -----------------------------------------------------------------------

    public function test_admin_can_create_user(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('users.create'))
            ->assertOk();
    }

    public function test_admin_can_store_user(): void
    {
        $this->actingAs($this->adminUser())
            ->post(route('users.store'), [
                'name'                  => 'New User',
                'email'                 => 'newuser@example.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'staff',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    }

    public function test_store_fails_with_duplicate_email(): void
    {
        $existing = User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->adminUser())
            ->post(route('users.store'), [
                'name'                  => 'Another',
                'email'                 => 'taken@example.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'staff',
            ])
            ->assertSessionHasErrors('email');
    }

    public function test_hr_cannot_access_create_user(): void
    {
        $this->actingAs($this->hrUser())
            ->get(route('users.create'))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Show
    // -----------------------------------------------------------------------

    public function test_admin_can_view_user_detail(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->adminUser())
            ->get(route('users.show', $user->id))
            ->assertOk();
    }

    // -----------------------------------------------------------------------
    // Edit / Update
    // -----------------------------------------------------------------------

    public function test_admin_can_update_another_user(): void
    {
        $target = User::factory()->create(['role' => 'staff']);

        $this->actingAs($this->adminUser())
            ->put(route('users.update', $target->id), [
                'name'  => 'Updated Name',
                'email' => $target->email,
                'role'  => 'HR',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Updated Name']);
    }

    public function test_admin_cannot_update_own_account_via_user_management(): void
    {
        $admin = $this->adminUser();

        // Controller redirects (not 403) when admin tries to update their own account
        $this->actingAs($admin)
            ->put(route('users.update', $admin->id), [
                'name'  => 'Trying to self-update',
                'email' => $admin->email,
                'role'  => 'admin',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    // -----------------------------------------------------------------------
    // Destroy
    // -----------------------------------------------------------------------

    public function test_admin_can_soft_delete_user(): void
    {
        $target = User::factory()->create();

        $this->actingAs($this->adminUser())
            ->delete(route('users.destroy', $target->id))
            ->assertRedirect();

        $this->assertSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $admin = $this->adminUser();

        // Controller redirects back with error (not 403) for self-delete
        $this->actingAs($admin)
            ->delete(route('users.destroy', $admin->id))
            ->assertRedirect()
            ->assertSessionHas('error');

        // Account must still exist (not deleted)
        $this->assertDatabaseHas('users', ['id' => $admin->id, 'deleted_at' => null]);
    }

    // -----------------------------------------------------------------------
    // Trash / Restore / Force Delete
    // -----------------------------------------------------------------------

    public function test_admin_can_view_trash(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('users.trash'))
            ->assertOk();
    }

    public function test_admin_can_restore_deleted_user(): void
    {
        $target = User::factory()->create();
        $target->delete();

        $this->actingAs($this->adminUser())
            ->post(route('users.restore', $target->id))
            ->assertRedirect();

        $this->assertNotSoftDeleted('users', ['id' => $target->id]);
    }

    public function test_admin_can_force_delete_user(): void
    {
        $target = User::factory()->create();
        $target->delete();

        $this->actingAs($this->adminUser())
            ->delete(route('users.force-delete', $target->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_demo_admin_cannot_create_user(): void
    {
        $demo = $this->demoAdmin();

        $this->actingAs($demo)
            ->post(route('users.store'), [
                'name'                  => 'Test',
                'email'                 => 'test@example.com',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
                'role'                  => 'staff',
            ])
            ->assertForbidden();
    }
}
