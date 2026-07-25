<?php

namespace Tests\Feature\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for ProfileController.
 */
class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_view_profile(): void
    {
        $this->actingAs($this->adminUser())
            ->get(route('profile.index'))
            ->assertOk();
    }

    public function test_user_can_update_name(): void
    {
        $user = $this->adminUser(['password' => bcrypt('currentpass')]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name'  => 'New Name',
                'email' => $user->email,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'New Name']);
    }

    public function test_demo_user_cannot_update_profile(): void
    {
        $demo = $this->demoAdmin();

        $this->actingAs($demo)
            ->put(route('profile.update'), [
                'name'  => 'Hacker',
                'email' => $demo->email,
            ])
            ->assertRedirect()
            ->assertSessionHas('error');
    }

    public function test_profile_update_with_password_change(): void
    {
        $user = $this->adminUser(['password' => bcrypt('oldpassword')]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name'             => $user->name,
                'email'            => $user->email,
                'current_password' => 'oldpassword',
                'password'         => 'newPassword1!',
                'password_confirmation' => 'newPassword1!',
            ])
            ->assertRedirect();

        // Ensure password changed
        $this->assertCredentials([
            'email'    => $user->email,
            'password' => 'newPassword1!',
        ]);
    }
}
