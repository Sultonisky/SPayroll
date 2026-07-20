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
        'base_salary',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    /**
     * Get all employees for the position.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
