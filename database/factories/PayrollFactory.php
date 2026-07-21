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

        // Track used employee+year+month combos to avoid unique constraint violation
        static $used = [];

        $attempt = 0;
        do {
            $year  = fake()->numberBetween(2024, 2026);
            $month = fake()->numberBetween(1, 12);
            $key   = "{$employee->id}-{$year}-{$month}";
            $attempt++;

            // After a few tries with same employee, pick a different employee
            if ($attempt > 10) {
                $employee = Employee::with('position')
                    ->inRandomOrder()
                    ->first();
                $attempt = 0;
            }
        } while (in_array($key, $used, true));

        $used[] = $key;

        // Base salary via model accessor (resolves by employee_type + position)
        $baseSalary = (float) ($employee->base_salary ?? fake()->numberBetween(5_000_000, 20_000_000));

        // Sum approved bonuses for this employee/period (if any exist)
        $bonus = (float) Bonus::forEmployee($employee->id)
            ->forPeriod($year, $month)
            ->approved()
            ->sum('amount');

        // Pay date: always on the 25th (or last day of month if < 25 days)
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $payDate     = sprintf('%04d-%02d-%02d', $year, $month, min(25, $daysInMonth));

        return [
            'employee_id'  => $employee->id,
            'year'         => $year,
            'month'        => $month,
            'pay_date'     => $payDate,
            'base_salary'  => $baseSalary,
            'bonus'        => $bonus,
            'total_salary' => $baseSalary + $bonus,
            'notes'        => fake()->optional(0.2)->sentence(),
            'status'       => fake()->randomElement(['draft', 'approved', 'approved', 'paid', 'paid']),
        ];
    }
}
