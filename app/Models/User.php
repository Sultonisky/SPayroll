<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Relations\HasMany; // TEMPORARILY DISABLED - used by attendance relations
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_demo',
        'foto',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_demo' => 'boolean',
        ];
    }

    protected $dates = ['deleted_at'];

    /**
     * Check if the user is a demo account.
     */
    public function isDemo(): bool
    {
        return (bool) $this->is_demo;
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is an operator.
     */
    public function isHR(): bool
    {
        return $this->role === 'HR';
    }

    /**
     * Check if the user is a staff.
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if the user is a staff.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff' || $this->role === 'HR' || $this->role === 'manager' || $this->role === 'admin';
    }

    /**
     * Get the foto URL.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto) {
            return null;
        }

        return Storage::disk('supabase')->url($this->foto);
    }

    // TEMPORARILY DISABLED - attendance feature not yet needed
    // public function attendanceImports(): HasMany
    // {
    //     return $this->hasMany(AttendanceImport::class, 'imported_by');
    // }

    // public function approvedAttendanceAdjustments(): HasMany
    // {
    //     return $this->hasMany(AttendanceAdjustment::class, 'approved_by');
    // }
}
