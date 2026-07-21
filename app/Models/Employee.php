<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'department_id',
        'position_id',
        'employee_code',
        'nik',
        'name',
        'gender',
        'email',
        'phone',
        'address',
        'join_date',
        'birth_date',
        'employee_status',
        'employee_type',
        'bank_name',
        'bank_account_number',
    ];

    protected $casts = [
        'join_date' => 'date',
        'birth_date' => 'date',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the base salary for this employee based on their type and position.
     */
    public function getBaseSalaryAttribute(): ?float
    {
        if (!$this->position) {
            return null;
        }

        return match($this->employee_type) {
            'fulltime'    => $this->position->base_salary_fulltime,
            'internship'  => $this->position->base_salary_internship,
            default       => null,
        };
    }

    /**
     * Get the user associated with the employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the department that owns the employee.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the position that owns the employee.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    /**
     * Get all payrolls for the employee.
     */
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    /**
     * Get all bonuses for the employee.
     */
    public function bonuses(): HasMany
    {
        return $this->hasMany(Bonus::class);
    }

    /**
     * Get all attendance records for the employee.
     * TEMPORARILY DISABLED - attendance feature not yet needed
     */
    // public function attendanceRecords(): HasMany
    // {
    //     return $this->hasMany(AttendanceRecord::class);
    // }
}
