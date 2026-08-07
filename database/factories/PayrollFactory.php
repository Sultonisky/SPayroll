<?php

namespace Database\Factories;

use App\Models\Bonus;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payroll>
 *
 * Formula: total_salary = base_salary + bonus
 * No allowances, no overtime, no deductions — by design.
 */
class PayrollFactory extends Factory
{
    public function definition(): array
    {
        $employee = Employee::with('position')->inRandomOrder()->first()
            ?? Employee::factory()->create();

        // Find a year/month combo not yet in the DB for this employee,
        // with a hard cap to prevent infinite loops.
        $maxAttempts = 50;
        $attempt = 0;
        $year = fake()->numberBetween(2024, 2026);
        $month = fake()->numberBetween(1, 12);

        while (
            $attempt < $maxAttempts &&
            Payroll::withTrashed()
                ->where('employee_id', $employee->id)
                ->where('year', $year)
                ->where('month', $month)
                ->exists()
        ) {
            $year = fake()->numberBetween(2024, 2026);
            $month = fake()->numberBetween(1, 12);
            $attempt++;

            // Switch to a different employee after several tries
            if ($attempt % 10 === 0) {
                $employee = Employee::with('position')->inRandomOrder()->first()
                    ?? Employee::factory()->create();
            }
        }

        // Base salary via model accessor (resolves by employee_type + position)
        $baseSalary = (float) ($employee->base_salary ?? fake()->numberBetween(5_000_000, 20_000_000));

        // Sum approved bonuses for this employee/period (if any exist)
        $bonus = (float) Bonus::forEmployee($employee->id)
            ->forPeriod($year, $month)
            ->approved()
            ->sum('amount');

        // Pay date: always on the 25th (or last day of month if < 25 days)
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $payDate = sprintf('%04d-%02d-%02d', $year, $month, min(25, $daysInMonth));

        return [
            'employee_id' => $employee->id,
            'year' => $year,
            'month' => $month,
            'pay_date' => $payDate,
            'base_salary' => $baseSalary,
            'bonus' => $bonus,
            'total_salary' => $baseSalary + $bonus,
            'notes' => fake()->optional(0.2)->sentence(),
            'status' => fake()->randomElement(['draft', 'approved', 'approved', 'paid', 'paid']),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }
}
