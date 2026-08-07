<?php

namespace Database\Seeders;

use App\Models\Bonus;
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

    public function run(): void
    {
        // ----------------------------------------------------------------
        // 1. Admin
        // ----------------------------------------------------------------
        $this->command->info('Seeding admin user...');
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@scroll.test')],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password123')),
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        // ----------------------------------------------------------------
        // 2. Demo accounts (fixed credentials for live demo)
        // ----------------------------------------------------------------
        $this->command->info('Seeding demo users...');
        $demoUsers = [
            ['name' => 'Demo Admin',   'email' => 'admin@demo.scroll.com',   'role' => 'admin'],
            ['name' => 'Demo HR',      'email' => 'hr@demo.scroll.com',      'role' => 'HR'],
            ['name' => 'Demo Manager', 'email' => 'manager@demo.scroll.com', 'role' => 'manager'],
            ['name' => 'Demo Staff',   'email' => 'staff@demo.scroll.com',   'role' => 'staff'],
            ['name' => 'Demo Finance', 'email' => 'finance@demo.scroll.com', 'role' => 'finance'],
        ];

        foreach ($demoUsers as $demo) {
            User::firstOrCreate(
                ['email' => $demo['email']],
                [
                    'name' => $demo['name'],
                    'password' => Hash::make('demo12345'),
                    'email_verified_at' => now(),
                    'role' => $demo['role'],
                    'is_demo' => true,
                ]
            );
        }

        // ----------------------------------------------------------------
        // 3. Supporting users
        // ----------------------------------------------------------------
        $this->command->info('Seeding supporting users...');
        User::factory()->hr()->count(2)->create();
        User::factory()->manager()->count(3)->create();
        User::factory()->finance()->count(2)->create();
        User::factory()->count(15)->create();

        // ----------------------------------------------------------------
        // 3. Departments
        // ----------------------------------------------------------------
        $this->command->info('Seeding departments...');
        Department::factory()->count(8)->create();

        // ----------------------------------------------------------------
        // 4. Positions
        // ----------------------------------------------------------------
        $this->command->info('Seeding positions...');
        Position::factory()->count(12)->create();

        // ----------------------------------------------------------------
        // 5. Employees
        // ----------------------------------------------------------------
        $this->command->info('Seeding employees...');
        Employee::factory()->count(30)->create();

        // ----------------------------------------------------------------
        // 6. Bonuses — seed before payrolls so PayrollFactory can sum them
        // ----------------------------------------------------------------
        $this->command->info('Seeding bonuses...');
        Bonus::factory()->approved()->count(40)->create();
        Bonus::factory()->pending()->count(10)->create();

        // ----------------------------------------------------------------
        // 7. Payrolls
        // ----------------------------------------------------------------
        $this->command->info('Seeding payrolls...');
        $payrollSeeded = 0;
        $payrollTarget = 60;
        $payrollAttempts = 0;
        $payrollMaxAttempts = 200;

        while ($payrollSeeded < $payrollTarget && $payrollAttempts < $payrollMaxAttempts) {
            try {
                Payroll::factory()->create();
                $payrollSeeded++;
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Duplicate employee/year/month combo — just retry
            }
            $payrollAttempts++;
        }

        // ----------------------------------------------------------------
        $this->command->info('');
        $this->command->info('All seed data created successfully.');
        $this->command->line('  Admin email : '.env('ADMIN_EMAIL', 'admin@scroll.com'));
        $this->command->line('  Admin pass  : '.env('ADMIN_PASSWORD', 'password123'));
        $this->command->info('');
        $this->command->info('Demo accounts (password: demo1234):');
        $this->command->line('  admin@demo.scroll.com   → Admin');
        $this->command->line('  hr@demo.scroll.com      → HR');
        $this->command->line('  manager@demo.scroll.com → Manager');
        $this->command->line('  staff@demo.scroll.com   → Staff');
        $this->command->line('  finance@demo.scroll.com → Finance');
    }
}
