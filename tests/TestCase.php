<?php

namespace Tests;

use App\Models\AuditLog;
use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Flush all observer listeners from models by default so tests don't trigger
     * DB notification writes on every factory create/update.
     * Tests that specifically need observers must call Model::observe() in their own setUp().
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->flushModelObservers();

        // Disable CSRF verification globally for feature tests.
        // In Laravel 13 the CSRF middleware is PreventRequestForgery (renamed from ValidateCsrfToken).
        // All our test routes are exercised via actingAs() so session integrity
        // is already guaranteed — CSRF adds no value in an in-process test.
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    /**
     * Remove all registered event listeners from the models that have observers,
     * without tearing down the event dispatcher itself.
     */
    protected function flushModelObservers(): void
    {
        foreach ([User::class, Employee::class, Payroll::class, Bonus::class, Department::class, Position::class] as $model) {
            $model::flushEventListeners();
        }
    }

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
