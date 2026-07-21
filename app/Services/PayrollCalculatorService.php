<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayrollCalculatorService
{
    /**
     * Calculate payroll components for a single employee in a given period.
     *
     * Formula: total_salary = base_salary + bonus
     *
     * @return array{
     *   employee_id: int,
     *   year: int,
     *   month: int,
     *   base_salary: float,
     *   bonus: float,
     *   total_salary: float,
     * }
     */
    public function calculate(Employee $employee, int $year, int $month): array
    {
        // Load position if not already loaded
        $employee->loadMissing('position');

        $baseSalary = (float) ($employee->base_salary ?? 0);

        $bonus = Bonus::forEmployee($employee->id)
            ->forPeriod($year, $month)
            ->approved()
            ->sum('amount');

        return [
            'employee_id'  => $employee->id,
            'year'         => $year,
            'month'        => $month,
            'base_salary'  => $baseSalary,
            'bonus'        => (float) $bonus,
            'total_salary' => $baseSalary + (float) $bonus,
        ];
    }

    /**
     * Generate draft payroll records for ALL active employees in a period.
     *
     * Skips employees that already have a payroll record for the same period.
     * Wraps everything in a transaction — if any insert fails, nothing is saved.
     *
     * @return array{ created: int, skipped: int }
     */
    public function generateBulk(int $year, int $month, string $payDate): array
    {
        $employees = Employee::with('position')
            ->where('employee_status', 'active')
            ->get();

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($employees, $year, $month, $payDate, &$created, &$skipped) {
            foreach ($employees as $employee) {
                // Skip if payroll already exists for this period
                $exists = Payroll::where('employee_id', $employee->id)
                    ->where('year', $year)
                    ->where('month', $month)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $components = $this->calculate($employee, $year, $month);

                Payroll::create([
                    'employee_id'  => $components['employee_id'],
                    'year'         => $components['year'],
                    'month'        => $components['month'],
                    'pay_date'     => $payDate,
                    'base_salary'  => $components['base_salary'],
                    'bonus'        => $components['bonus'],
                    'total_salary' => $components['total_salary'],
                    'status'       => 'draft',
                ]);

                $created++;
            }
        });

        return ['created' => $created, 'skipped' => $skipped];
    }
}
