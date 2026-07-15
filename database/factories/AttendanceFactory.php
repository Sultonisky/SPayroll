<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $year = fake()->year();
        $month = fake()->numberBetween(1, 12);
        $workDays = fake()->numberBetween(20, 23);
        $present = fake()->numberBetween(15, $workDays);
        $remaining = $workDays - $present;
        $sick = fake()->numberBetween(0, min(2, $remaining));
        $remaining -= $sick;
        $leave = fake()->numberBetween(0, min(3, $remaining));
        $remaining -= $leave;
        $alpha = $remaining;

        return [
            'employee_id' => Employee::inRandomOrder()->first()?->id ?? Employee::factory()->create()->id,
            'year' => $year,
            'month' => $month,
            'work_days' => $workDays,
            'present' => $present,
            'sick' => $sick,
            'leave' => $leave,
            'alpha' => $alpha,
            'overtime_hours' => fake()->numberBetween(0, 40),
            'notes' => fake()->optional()->paragraph(),
        ];
    }
}
