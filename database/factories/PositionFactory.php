<?php

namespace Database\Factories;

use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $positions = [
            'Staff' => 3500000,
            'Senior Staff' => 5000000,
            'Supervisor' => 7500000,
            'Manager' => 12000000,
            'Senior Manager' => 18000000,
            'Director' => 25000000,
        ];

        $positionName = fake()->unique()->randomElement(array_keys($positions));

        return [
            'name' => $positionName,
            'description' => fake()->paragraph(),
            'base_salary' => $positions[$positionName],
        ];
    }
}
