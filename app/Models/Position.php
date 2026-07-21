<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'base_salary_fulltime',
        'base_salary_internship',
    ];

    protected $casts = [
        'base_salary_fulltime'   => 'float',
        'base_salary_internship' => 'float',
        'deleted_at'             => 'datetime',
    ];

    /**
     * Get the base salary for a given employee type.
     */
    public function getBaseSalaryFor(string $employeeType): ?float
    {
        return match($employeeType) {
            'fulltime'   => $this->base_salary_fulltime,
            'internship' => $this->base_salary_internship,
            default      => null,
        };
    }

    /**
     * Get all employees for the position.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
