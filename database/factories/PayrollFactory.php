<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payroll>
 */
class PayrollFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employee = Employee::inRandomOrder()->first() ?? Employee::factory()->create();
        $year = fake()->year();
        $month = fake()->numberBetween(1, 12);
        $baseSalary = $employee->position->base_salary ?? fake()->numberBetween(3000000, 30000000);
        $allowances = fake()->numberBetween(500000, 3000000);
        $bonus = fake()->optional(0.3)->numberBetween(500000, 5000000) ?? 0;
        $overtimeHours = fake()->numberBetween(0, 40);
        $overtimePay = $overtimeHours * 150000; // contoh rate lembur
        $deductions = fake()->numberBetween(200000, 1500000);
        $totalSalary = $baseSalary + $allowances + $bonus + $overtimePay - $deductions;

        return [
            'employee_id' => $employee->id,
            'attendance_id' => Attendance::where('employee_id', $employee->id)->where('year', $year)->where('month', $month)->first()?->id ?? null,
            'year' => $year,
            'month' => $month,
            'pay_date' => fake()->dateTimeBetween("$year-$month-01", "$year-$month-30")->format('Y-m-d'),
            'base_salary' => $baseSalary,
            'allowances' => $allowances,
            'bonus' => $bonus,
            'overtime_pay' => $overtimePay,
            'deductions' => $deductions,
            'total_salary' => max($totalSalary, 0), // jangan sampai minus
            'notes' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(['draft', 'approved', 'paid']),
        ];
    }
}
