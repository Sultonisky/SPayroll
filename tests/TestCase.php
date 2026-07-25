<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /** Helper: create and return an authenticated admin (non-demo). */
    protected function adminUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'admin', 'is_demo' => false], $attributes));
    }

    /** Helper: create and return an authenticated demo admin. */
    protected function demoAdmin(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'admin', 'is_demo' => true], $attributes));
    }

    /** Helper: create an HR user. */
    protected function hrUser(array $attributes = []): User
    {
        return User::factory()->hr()->create($attributes);
    }

    /** Helper: create a manager user. */
    protected function managerUser(array $attributes = []): User
    {
        return User::factory()->manager()->create($attributes);
    }

    /** Helper: create a staff user. */
    protected function staffUser(array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => 'staff'], $attributes));
    }
}
