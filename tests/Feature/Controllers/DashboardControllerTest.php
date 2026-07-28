<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for DashboardController.
 */
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_roles_can_access_dashboard(): void
    {
        foreach ([
            $this->adminUser(),
            $this->hrUser(),
            $this->managerUser(),
            $this->staffUser(),
        ] as $user) {
            $this->actingAs($user)
                ->get(route('dashboard'))
                ->assertOk();
        }
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_dashboard_renders_expected_data(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('totalEmployees')
            ->assertViewHas('activeEmployees')
            ->assertViewHas('allTimePaidTotal');
    }
}
