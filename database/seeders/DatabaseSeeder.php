<?php

namespace Database\Seeders;

use App\Models\AttendanceImport;
use App\Models\AttendanceRecord;
use App\Models\AttendanceAdjustment;
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

        // 5. Seed Attendance Imports
        $this->command->info('Seeding Attendance Imports...');
        $imports = AttendanceImport::factory()->count(5)->create();

        // 6. Seed Attendance Records
        $this->command->info('Seeding Attendance Records...');
        $records = collect();
        foreach ($employees as $employee) {
            // Create records for last 30 days
            for ($i = 0; $i < 30; $i++) {
                $record = AttendanceRecord::factory()->create([
                    'employee_id' => $employee->id,
                    'attendance_import_id' => $imports->random()->id,
                    'attendance_date' => now()->subDays($i)->format('Y-m-d'),
                ]);
                $records->push($record);
            }
        }

        // 7. Seed Attendance Adjustments
        $this->command->info('Seeding Attendance Adjustments...');
        AttendanceAdjustment::factory()->count(10)->create([
            'attendance_record_id' => $records->random()->id,
        ]);

        $this->command->info('All dummy data seeded successfully!');
    }
}
