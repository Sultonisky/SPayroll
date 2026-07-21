<?php

namespace Database\Factories;

use App\Models\Bonus;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bonus>
 */
class BonusFactory extends Factory
{
    /**
     * Common bonus types for a remote-first company.
     * Kept general so it works for software house, agency, consulting, etc.
     */
    private static array $types = [
        'Performance Bonus',
        'Project Completion Bonus',
        'Referral Bonus',
        'Annual Bonus',
        'Retention Bonus',
        'Special Achievement',
    ];

    public function definition(): array
    {
        $employee = Employee::inRandomOrder()->first() ?? Employee::factory()->create();
        $year     = fake()->numberBetween(2024, 2026);
        $month    = fake()->numberBetween(1, 12);
        $status   = fake()->randomElement(['pending', 'approved', 'approved', 'rejected']);

        $approvedBy = null;
        $approvedAt = null;

        if ($status === 'approved' || $status === 'rejected') {
            $approvedBy = User::whereIn('role', ['admin', 'HR'])->inRandomOrder()->first()?->id;
            $approvedAt = fake()->dateTimeBetween("-{$year} years", 'now');
        }

        return [
            'employee_id' => $employee->id,
            'year'        => $year,
            'month'       => $month,
            'type'        => fake()->randomElement(self::$types),
            'description' => fake()->optional(0.6)->sentence(),
            'amount'      => fake()->randomElement([
                500_000, 750_000, 1_000_000, 1_500_000,
                2_000_000, 2_500_000, 3_000_000,
            ]),
            'status'      => $status,
            'approved_by' => $approvedBy,
            'approved_at' => $approvedAt,
            'notes'       => $status === 'rejected' ? fake()->sentence() : null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => 'approved',
            'approved_by' => User::whereIn('role', ['admin', 'HR'])->inRandomOrder()->first()?->id,
            'approved_at' => now(),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'      => 'pending',
            'approved_by' => null,
            'approved_at' => null,
        ]);
    }
}
