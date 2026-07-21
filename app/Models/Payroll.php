<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'pay_date',
        'base_salary',
        'bonus',
        'total_salary',
        'notes',
        'status',
    ];

    protected $casts = [
        'base_salary'  => 'float',
        'bonus'        => 'float',
        'total_salary' => 'float',
        'pay_date'     => 'date',
        'deleted_at'   => 'datetime',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isDraft(): bool    { return $this->status === 'draft'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isPaid(): bool     { return $this->status === 'paid'; }

    public function monthName(): string
    {
        return \Carbon\Carbon::create($this->year, $this->month)->translatedFormat('F Y');
    }
}
