<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $departments = [
            'Information Technology (IT)',
            'Human Resource Development (HRD)',
            'Finance',
            'Marketing',
            'Production',
            'Logistics',
            'Customer Service',
            'Research and Development (R&D)'
        ];

        return [
            'name' => fake()->unique()->randomElement($departments),
            'description' => fake()->paragraph(),
        ];
    }
}
