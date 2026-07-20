<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AttendanceImport>
 */
class AttendanceImportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalRows = fake()->numberBetween(10, 100);
        $successRows = fake()->numberBetween(8, $totalRows);
        $failedRows = $totalRows - $successRows;

        return [
            'file_name' => fake()->word() . '_' . fake()->date('Y-m-d') . '.' . fake()->randomElement(['xlsx', 'csv']),
            'imported_by' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
            'total_rows' => $totalRows,
            'success_rows' => $successRows,
            'failed_rows' => $failedRows,
            'status' => fake()->randomElement(['pending', 'completed', 'failed']),
            'imported_at' => now(),
        ];
    }
}