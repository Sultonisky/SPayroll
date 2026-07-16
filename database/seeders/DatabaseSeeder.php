<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin
        $adminEmail = env('ADMIN_EMAIL', 'admin@spayroll.com');
        $adminName = env('ADMIN_NAME', 'Administrator');
        $adminPassword = env('ADMIN_PASSWORD', 'password123');

        User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name'              => $adminName,
                'password'          => Hash::make($adminPassword),
                'email_verified_at' => now(),
                'role'              => 'admin',
            ]
        );

        $this->command->info('Admin user created/updated successfully!');

        // 1b. Seed Regular Users
        $this->command->info('Seeding Regular Users...');
        User::factory()->count(10)->create();

        // 2. Seed Departments
        $this->command->info('Seeding Departments...');
        Department::factory()->count(5)->create();

        // 3. Seed Positions
        $this->command->info('Seeding Positions...');
        Position::factory()->count(6)->create();

        // 4. Seed Employees
        $this->command->info('Seeding Employees...');
        $employees = Employee::factory()->count(20)->create();

        // 5. Seed Attendances and Payrolls for each Employee (last 6 months)
        $this->command->info('Seeding Attendances and Payrolls...');
        $currentYear = now()->year;
        $currentMonth = now()->month;

        foreach ($employees as $employee) {
            for ($i = 0; $i < 6; $i++) {
                $month = $currentMonth - $i;
                $year = $currentYear;
                if ($month <= 0) {
                    $month += 12;
                    $year -= 1;
                }

                // Create Attendance
                $attendance = Attendance::factory()->create([
                    'employee_id' => $employee->id,
                    'year' => $year,
                    'month' => $month,
                ]);

                // Create Payroll linked to Attendance
                Payroll::factory()->create([
                    'employee_id' => $employee->id,
                    'attendance_id' => $attendance->id,
                    'year' => $year,
                    'month' => $month,
                    'base_salary' => $employee->base_salary,
                ]);
            }
        }

        $this->command->info('All dummy data seeded successfully!');
    }
}
