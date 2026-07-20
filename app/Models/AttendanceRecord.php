<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_import_id',
        'attendance_date',
        'check_in',
        'check_out',
        'work_hours',
        'late_minutes',
        'overtime_minutes',
        'attendance_status',
        'notes',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function attendanceImport(): BelongsTo
    {
        return $this->belongsTo(AttendanceImport::class);
    }

    public function attendanceAdjustments(): HasMany
    {
        return $this->hasMany(AttendanceAdjustment::class);
    }
}
