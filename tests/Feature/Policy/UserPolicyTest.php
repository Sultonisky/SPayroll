<?php

namespace Tests\Feature\Policy;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for UserPolicy — covers all abilities including demo and self-action guards.
 */
class UserPolicyTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Gate::before — non-demo admin bypasses everything
    // -----------------------------------------------------------------------

    public function test_non_demo_admin_can_do_everything(): void
    {
        $admin  = $this->adminUser();
        $target = $this->hrUser();

        $this->assertTrue($admin->can('viewAny', User::class));
        $this->assertTrue($admin->can('view', $target));
        $this->assertTrue($admin->can('create', User::class));
        $this->assertTrue($admin->can('update', $target));
        $this->assertTrue($admin->can('delete', $target));
    }

    // -----------------------------------------------------------------------
    // viewAny / view
    // -----------------------------------------------------------------------

    public function test_non_admin_cannot_view_any_users(): void
    {
        foreach ([$this->hrUser(), $this->managerUser(), $this->staffUser()] as $user) {
            $this->assertFalse($user->can('viewAny', User::class));
        }
    }

    public function test_non_admin_cannot_view_user_detail(): void
    {
        $target = $this->adminUser();
        foreach ([$this->hrUser(), $this->managerUser(), $this->staffUser()] as $user) {
            $this->assertFalse($user->can('view', $target));
        }
    }

    // -----------------------------------------------------------------------
    // create
    // -----------------------------------------------------------------------

    public function test_demo_admin_cannot_create_users(): void
    {
        $demo = $this->demoAdmin();

        $this->assertFalse($demo->can('create', User::class));
    }

    public function test_non_admin_cannot_create_users(): void
    {
        $this->assertFalse($this->hrUser()->can('create', User::class));
        $this->assertFalse($this->staffUser()->can('create', User::class));
    }

    // -----------------------------------------------------------------------
    // update — self-action guard
    // -----------------------------------------------------------------------

    public function test_admin_cannot_update_themselves(): void
    {
        $admin = $this->demoAdmin(); // use demo so Gate::before is bypassed → policy runs

        $this->assertFalse($admin->can('update', $admin));
    }

    public function test_demo_admin_cannot_update_other_users(): void
    {
        $demo   = $this->demoAdmin();
        $target = $this->hrUser();

        $this->assertFalse($demo->can('update', $target));
    }

    // -----------------------------------------------------------------------
    // delete — self-action guard
    // -----------------------------------------------------------------------

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = $this->demoAdmin();

        $this->assertFalse($admin->can('delete', $admin));
    }

    public function test_demo_admin_cannot_delete_others(): void
    {
        $demo   = $this->demoAdmin();
        $target = $this->hrUser();

        $this->assertFalse($demo->can('delete', $target));
    }

    // -----------------------------------------------------------------------
    // restore / forceDelete
    // -----------------------------------------------------------------------

    public function test_admin_cannot_restore_themselves(): void
    {
        $admin = $this->demoAdmin();

        $this->assertFalse($admin->can('restore', $admin));
    }

    public function test_admin_cannot_force_delete_themselves(): void
    {
        $admin = $this->demoAdmin();

        $this->assertFalse($admin->can('forceDelete', $admin));
    }

    public function test_demo_admin_cannot_force_delete_others(): void
    {
        $demo   = $this->demoAdmin();
        $target = $this->hrUser();

        $this->assertFalse($demo->can('forceDelete', $target));
    }
}
