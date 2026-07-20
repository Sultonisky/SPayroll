<?php

namespace Database\Factories;

use App\Models\AttendanceImport;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\AttendanceRecord>
 */
class AttendanceRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkIn = fake()->time('H:i:s', '09:00:00');
        $checkOut = fake()->time('H:i:s', '18:00:00');
        
        $record = new \App\Models\AttendanceRecord([
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);
        $attendanceService = new AttendanceService();
        $calculated = $attendanceService->calculateAttendanceData($record);

        return [
            'employee_id' => Employee::inRandomOrder()->first()?->id ?? Employee::factory()->create()->id,
            'attendance_import_id' => AttendanceImport::inRandomOrder()->first()?->id ?? AttendanceImport::factory()->create()->id,
            'attendance_date' => fake()->dateTimeBetween('-2 months', 'now')->format('Y-m-d'),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'work_hours' => $calculated['work_hours'],
            'late_minutes' => $calculated['late_minutes'],
            'overtime_minutes' => $calculated['overtime_minutes'],
            'attendance_status' => $calculated['attendance_status'],
            'notes' => fake()->optional()->sentence(),
        ];
    }
}