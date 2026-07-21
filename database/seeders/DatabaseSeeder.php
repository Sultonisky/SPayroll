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
            ['email' => env('ADMIN_EMAIL', 'admin@spayroll.com')],
            [
                'name'              => env('ADMIN_NAME', 'Administrator'),
                'password'          => Hash::make(env('ADMIN_PASSWORD', 'password123')),
                'email_verified_at' => now(),
                'role'              => 'admin',
            ]
        );

        // ----------------------------------------------------------------
        // 2. Supporting users
        // ----------------------------------------------------------------
        $this->command->info('Seeding supporting users...');
        User::factory()->hr()->count(2)->create();
        User::factory()->manager()->count(3)->create();
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
        Payroll::factory()->count(60)->create();

        // ----------------------------------------------------------------
        $this->command->info('');
        $this->command->info('All seed data created successfully.');
        $this->command->line('  Admin email : ' . env('ADMIN_EMAIL', 'admin@spayroll.com'));
        $this->command->line('  Admin pass  : ' . env('ADMIN_PASSWORD', 'password123'));
    }
}
