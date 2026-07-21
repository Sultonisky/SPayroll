<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Bonus extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'year',
        'month',
        'type',
        'description',
        'amount',
        'status',
        'approved_by',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'amount'      => 'float',
        'approved_at' => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ----------------------------------------------------------------
    // Scopes
    // ----------------------------------------------------------------

    /** Only approved bonuses (used by PayrollCalculatorService). */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /** Filter by period. */
    public function scopeForPeriod(Builder $query, int $year, int $month): Builder
    {
        return $query->where('year', $year)->where('month', $month);
    }

    /** Filter by employee. */
    public function scopeForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    public function isPending(): bool   { return $this->status === 'pending'; }
    public function isApproved(): bool  { return $this->status === 'approved'; }
    public function isRejected(): bool  { return $this->status === 'rejected'; }
}
