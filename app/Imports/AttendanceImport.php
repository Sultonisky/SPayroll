<?php

namespace App\Imports;

use App\Models\Attendance;
use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;

class AttendanceImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use Importable;

    /**
     * @var array $failures
     */
    public $failures = [];

    /**
     * @var int $rowNumber
     */
    protected $rowNumber = 2; // Start at 2 because heading is row 1

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $currentRow = $this->rowNumber++;

        // Check if either employee_id or employee_name is present in the row first
        if (empty($row['employee_id']) && empty($row['employee_name'])) {
            $this->failures[] = [
                'row' => 'Baris ' . $currentRow,
                'error' => 'Setidaknya satu dari employee_id atau employee_name harus diisi'
            ];
            return null;
        }

        // Find employee by id or name
        $employee = Employee::find($row['employee_id']) ?? Employee::where('name', $row['employee_name'])->first();

        if (!$employee) {
            $this->failures[] = [
                'row' => 'Baris ' . $currentRow,
                'error' => 'Employee tidak ditemukan (employee_id/employee_name tidak cocok)'
            ];
            return null;
        }

        // Check if attendance already exists
        $exists = Attendance::where('employee_id', $employee->id)
            ->where('year', $row['year'])
            ->where('month', $row['month'])
            ->exists();

        if ($exists) {
            $this->failures[] = [
                'row' => 'Baris ' . $currentRow,
                'error' => "Attendance untuk {$employee->name} (tahun {$row['year']}, bulan {$row['month']}) sudah ada"
            ];
            return null;
        }

        return new Attendance([
            'employee_id' => $employee->id,
            'year' => $row['year'],
            'month' => $row['month'],
            'work_days' => $row['work_days'] ?? 0,
            'present' => $row['present'] ?? 0,
            'sick' => $row['sick'] ?? 0,
            'leave' => $row['leave'] ?? 0,
            'alpha' => $row['alpha'] ?? 0,
            'overtime_hours' => $row['overtime_hours'] ?? 0,
            'notes' => $row['notes'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'nullable|exists:employees,id',
            'employee_name' => 'nullable|string',
            'year' => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'work_days' => 'nullable|integer|min:0',
            'present' => 'nullable|integer|min:0',
            'sick' => 'nullable|integer|min:0',
            'leave' => 'nullable|integer|min:0',
            'alpha' => 'nullable|integer|min:0',
            'overtime_hours' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = [
                'row' => 'Baris ' . ($failure->row() + 1),
                'error' => implode(', ', $failure->errors())
            ];
        }
    }
}
