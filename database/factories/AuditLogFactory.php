<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * All possible actions that can appear in audit logs.
     */
    private static array $actions = [
        'created', 'updated', 'deleted', 'restored', 'force_deleted',
        'login', 'logout', 'login_failed',
        'approved', 'rejected', 'mark_paid', 'export',
    ];

    /**
     * Auditable model types tracked by the system.
     */
    private static array $modelTypes = [
        'App\\Models\\User',
        'App\\Models\\Employee',
        'App\\Models\\Department',
        'App\\Models\\Position',
        'App\\Models\\Payroll',
        'App\\Models\\Bonus',
    ];

    public function definition(): array
    {
        $action = fake()->randomElement(self::$actions);
        $modelType = fake()->randomElement(self::$modelTypes);

        // Only include old/new values for update events
        $oldValues = null;
        $newValues = null;

        if ($action === 'updated') {
            $oldValues = ['name' => fake()->name(), 'email' => fake()->email()];
            $newValues = ['name' => fake()->name(), 'email' => fake()->email()];
        } elseif ($action === 'created') {
            $newValues = ['name' => fake()->name(), 'email' => fake()->email()];
        }

        // Auth events have no auditable model
        $isAuthEvent = in_array($action, ['login', 'logout', 'login_failed']);

        return [
            'user_id'        => User::factory(),
            'action'         => $action,
            'auditable_type' => $isAuthEvent ? null : $modelType,
            'auditable_id'   => $isAuthEvent ? null : fake()->numberBetween(1, 100),
            'description'    => fake()->sentence(),
            'old_values'     => $oldValues,
            'new_values'     => $newValues,
            'ip_address'     => fake()->ipv4(),
            'user_agent'     => fake()->userAgent(),
            'url'            => fake()->url(),
            'method'         => fake()->randomElement(['GET', 'POST', 'PUT', 'DELETE', 'PATCH']),
            'created_at'     => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }

    // -----------------------------------------------------------------------
    // Named states — mirror actions used across the system
    // -----------------------------------------------------------------------

    public function created(): static
    {
        return $this->state(fn () => [
            'action'      => 'created',
            'old_values'  => null,
            'new_values'  => ['name' => fake()->name()],
        ]);
    }

    public function updated(): static
    {
        return $this->state(fn () => [
            'action'     => 'updated',
            'old_values' => ['name' => fake()->name()],
            'new_values' => ['name' => fake()->name()],
        ]);
    }

    public function deleted(): static
    {
        return $this->state(fn () => [
            'action'     => 'deleted',
            'old_values' => null,
            'new_values' => null,
        ]);
    }

    public function restored(): static
    {
        return $this->state(fn () => [
            'action'     => 'restored',
            'old_values' => null,
            'new_values' => null,
        ]);
    }

    public function forceDeleted(): static
    {
        return $this->state(fn () => [
            'action'         => 'force_deleted',
            'auditable_type' => null,
            'auditable_id'   => null,
            'old_values'     => null,
            'new_values'     => null,
        ]);
    }

    public function login(): static
    {
        return $this->state(fn () => [
            'action'         => 'login',
            'auditable_type' => null,
            'auditable_id'   => null,
            'old_values'     => null,
            'new_values'     => null,
        ]);
    }

    public function loginFailed(): static
    {
        return $this->state(fn () => [
            'action'         => 'login_failed',
            'auditable_type' => null,
            'auditable_id'   => null,
            'old_values'     => null,
            'new_values'     => null,
        ]);
    }

    public function logout(): static
    {
        return $this->state(fn () => [
            'action'         => 'logout',
            'auditable_type' => null,
            'auditable_id'   => null,
            'old_values'     => null,
            'new_values'     => null,
        ]);
    }

    public function forModel(string $modelClass, int $modelId): static
    {
        return $this->state(fn () => [
            'auditable_type' => $modelClass,
            'auditable_id'   => $modelId,
        ]);
    }

    public function byUser(User $user): static
    {
        return $this->state(fn () => ['user_id' => $user->id]);
    }

    public function fromIp(string $ip): static
    {
        return $this->state(fn () => ['ip_address' => $ip]);
    }

    /** Generate a log without a linked user (e.g. system/unauthenticated event). */
    public function anonymous(): static
    {
        return $this->state(fn () => ['user_id' => null]);
    }
}
