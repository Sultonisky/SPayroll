<?php

namespace Tests\Feature\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for RoleMiddleware — verifies role-based route access.
 */
class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Admin-only routes (/users)
    // -----------------------------------------------------------------------

    public function test_admin_can_access_users_index(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('users.index'))
            ->assertOk();
    }

    public function test_hr_cannot_access_users_index(): void
    {
        $this->actingAs($this->hrUser())
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_manager_cannot_access_users_index(): void
    {
        $this->actingAs($this->managerUser())
            ->get(route('users.index'))
            ->assertForbidden();
    }

    public function test_staff_cannot_access_users_index(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('users.index'))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Admin + HR + Manager routes (/departments)
    // -----------------------------------------------------------------------

    public function test_admin_hr_manager_can_access_departments(): void
    {
        foreach ([$this->adminUser(), $this->hrUser(), $this->managerUser()] as $user) {
            $this->actingAs($user)
                ->get(route('departments.index'))
                ->assertOk();
        }
    }

    public function test_staff_cannot_access_departments_index(): void
    {
        $this->actingAs($this->staffUser())
            ->get(route('departments.index'))
            ->assertForbidden();
    }

    // -----------------------------------------------------------------------
    // Employees — all roles can view
    // -----------------------------------------------------------------------

    public function test_all_roles_can_access_employees_index(): void
    {
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

    // -----------------------------------------------------------------------
    // Guest blocked
    // -----------------------------------------------------------------------

    public function test_guest_cannot_access_any_dashboard_route(): void
    {
        $routes = [
            route('admin.dashboard'),
            route('users.index'),
            route('employees.index'),
            route('payrolls.index'),
            route('bonuses.index'),
        ];

        foreach ($routes as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }
}
