<?php

namespace Database\Factories;

use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AttendanceAdjustment>
 */
class AttendanceAdjustmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $record = AttendanceRecord::inRandomOrder()->first() ?? AttendanceRecord::factory()->create();

        return [
            'attendance_record_id' => $record->id,
            'old_check_in' => $record->check_in,
            'new_check_in' => fake()->time('H:i:s', '08:00:00'),
            'old_check_out' => $record->check_out,
            'new_check_out' => fake()->time('H:i:s', '17:00:00'),
            'reason' => fake()->sentence(),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'approved_by' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
        ];
    }
}