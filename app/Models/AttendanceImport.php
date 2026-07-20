<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_name',
        'imported_by',
        'total_rows',
        'success_rows',
        'failed_rows',
        'status',
        'imported_at',
    ];

    protected $casts = [
        'imported_at' => 'datetime',
    ];

    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }
}
