<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Common remote-work allowances for a software house / digital agency (IDR).
     * These are realistic for Indonesian tech companies.
     */
    private const ALLOWANCE_RANGE  = [500_000,  2_000_000]; // internet, electricity, etc.
    private const BONUS_CHANCE      = 0.25;                  // 25% chance of performance bonus
    private const BONUS_RANGE       = [500_000,  3_000_000];
    private const OVERTIME_RATE     = 100_000;               // per hour (remote overtime)
    private const OVERTIME_MAX_HRS  = 20;
    private const DEDUCTION_RANGE   = [100_000,  500_000];   // BPJS, etc.

    public function definition(): array
    {
        $employee = Employee::with('position')->inRandomOrder()->first()
            ?? Employee::factory()->create();

        // Resolve base salary via model accessor (respects employee_type)
        $baseSalary = $employee->base_salary
            ?? fake()->numberBetween(5_000_000, 20_000_000);

        $year  = fake()->numberBetween(2023, 2026);
        $month = fake()->numberBetween(1, 12);

        // Keep pay_date within valid range for the month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $payDay      = min(25, $daysInMonth); // payroll usually on 25th
        $payDate     = sprintf('%04d-%02d-%02d', $year, $month, $payDay);

        $allowances   = fake()->numberBetween(...self::ALLOWANCE_RANGE);
        $bonus        = fake()->boolean((int)(self::BONUS_CHANCE * 100))
                            ? fake()->numberBetween(...self::BONUS_RANGE)
                            : 0;
        $overtimeHrs  = fake()->numberBetween(0, self::OVERTIME_MAX_HRS);
        $overtimePay  = $overtimeHrs * self::OVERTIME_RATE;
        $deductions   = fake()->numberBetween(...self::DEDUCTION_RANGE);
        $totalSalary  = max(0, $baseSalary + $allowances + $bonus + $overtimePay - $deductions);

        return [
            'employee_id'  => $employee->id,
            'year'         => $year,
            'month'        => $month,
            'pay_date'     => $payDate,
            'base_salary'  => $baseSalary,
            'allowances'   => $allowances,
            'bonus'        => $bonus,
            'overtime_pay' => $overtimePay,
            'deductions'   => $deductions,
            'total_salary' => $totalSalary,
            'notes'        => fake()->optional(0.3)->sentence(),
            'status'       => fake()->randomElement(['draft', 'approved', 'approved', 'paid', 'paid']), // bias towards processed
        ];
    }
}
