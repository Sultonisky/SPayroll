<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for authentication (login / logout).
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Login page
    // -----------------------------------------------------------------------

    public function test_login_page_renders(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_authenticated_user_redirected_away_from_login(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('login'))
            ->assertRedirect();
    }

    // -----------------------------------------------------------------------
    // Successful login
    // -----------------------------------------------------------------------

    public function test_admin_can_login(): void
    {
        $user = $this->adminUser(['password' => bcrypt('password')]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_hr_can_login(): void
    {
        $user = $this->hrUser(['password' => bcrypt('password')]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_manager_can_login(): void
    {
        $user = $this->managerUser(['password' => bcrypt('password')]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertAuthenticated();
    }

    public function test_staff_can_login(): void
    {
        $user = $this->staffUser(['password' => bcrypt('password')]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect();

        $this->assertAuthenticated();
    }

    // -----------------------------------------------------------------------
    // Failed login
    // -----------------------------------------------------------------------

    public function test_login_fails_with_wrong_password(): void
    {
        $user = $this->adminUser(['password' => bcrypt('correctpassword')]);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ])->assertRedirect();

        $this->assertGuest();
    }

    public function test_login_fails_with_nonexistent_email(): void
    {
        $this->post(route('login'), [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_fails_with_missing_email(): void
    {
        $this->post(route('login'), [
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }

    public function test_login_fails_with_missing_password(): void
    {
        $user = $this->adminUser();

        $this->post(route('login'), [
            'email' => $user->email,
        ])->assertSessionHasErrors('password');
    }

    // -----------------------------------------------------------------------
    // Logout
    // -----------------------------------------------------------------------

    public function test_authenticated_user_can_logout(): void
    {
        $user = $this->adminUser();

        $this->actingAs($user)
            ->post(route('admin.logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    // -----------------------------------------------------------------------
    // Rate limiting
    // -----------------------------------------------------------------------

    public function test_login_is_rate_limited_after_five_attempts(): void
    {
        $user = $this->adminUser();

        // Exhaust 5 attempts
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong',
        ]);

        $response->assertStatus(429);
    }
}
