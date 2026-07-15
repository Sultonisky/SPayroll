<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $position = Position::inRandomOrder()->first() ?? Position::factory()->create();
        $baseSalary = $position->base_salary ?? fake()->numberBetween(3000000, 30000000);

        return [
            'department_id' => Department::inRandomOrder()->first()?->id ?? Department::factory()->create()->id,
            'position_id' => $position->id,
            'nik' => fake()->unique()->numerify('##########'),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->unique()->phoneNumber(),
            'address' => fake()->address(),
            'join_date' => fake()->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
            'birth_date' => fake()->dateTimeBetween('-55 years', '-22 years')->format('Y-m-d'),
            'status' => fake()->randomElement(['active', 'inactive', 'resigned']),
            'base_salary' => $baseSalary,
        ];
    }
}
