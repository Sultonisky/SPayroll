<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Indonesian-style bank names common in remote/tech companies.
     */
    private static array $banks = ['BCA', 'Mandiri', 'BNI', 'BRI', 'CIMB Niaga', 'Jenius (BTPN)', 'SeaBank', 'Jago'];

    /**
     * Indonesian-style provinces for addresses (remote workers spread across Indonesia).
     */
    private static array $cities = [
        'Jakarta Selatan', 'Jakarta Barat', 'Bandung', 'Yogyakarta', 'Surabaya',
        'Bali (Denpasar)', 'Medan', 'Makassar', 'Semarang', 'Bogor',
        'Tangerang Selatan', 'Malang', 'Solo', 'Palembang', 'Balikpapan',
    ];

    public function definition(): array
    {
        $position = Position::inRandomOrder()->first() ?? Position::factory()->create();
        $type     = fake()->randomElement(['fulltime', 'internship']);

        // Sequential employee_code (e.g. 001, 002, ...)
        static $counter = null;
        if ($counter === null) {
            $latest  = Employee::withTrashed()
                ->whereNotNull('employee_code')
                ->orderByDesc('employee_code')
                ->value('employee_code');
            $counter = $latest ? ((int) $latest) + 1 : 1;
        }
        $employeeCode = str_pad($counter++, 3, '0', STR_PAD_LEFT);

        $city    = fake()->randomElement(self::$cities);
        $street  = fake()->streetAddress();

        return [
            'department_id'       => Department::inRandomOrder()->first()?->id ?? Department::factory()->create()->id,
            'position_id'         => $position->id,
            'employee_code'       => $employeeCode,
            'nik'                 => fake()->unique()->numerify('3###############'), // 16 digit KTP-like
            'name'                => fake()->name(),
            'gender'              => fake()->randomElement(['laki-laki', 'perempuan']),
            'email'               => fake()->unique()->safeEmail(),
            'phone'               => fake()->unique()->numerify('08##########'),    // Indonesian mobile format
            'address'             => "{$street}, {$city}",
            'join_date'           => fake()->dateTimeBetween('-5 years', '-1 month')->format('Y-m-d'),
            'birth_date'          => fake()->dateTimeBetween('-40 years', '-22 years')->format('Y-m-d'),
            'employee_status'     => fake()->randomElement(['active', 'active', 'active', 'inactive', 'resigned']), // bias towards active
            'employee_type'       => $type,
            'bank_name'           => fake()->randomElement(self::$banks),
            'bank_account_number' => fake()->unique()->numerify('##########'),
        ];
    }
}
