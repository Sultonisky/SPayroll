<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Relations\HasMany; // TEMPORARILY DISABLED - used by attendance relations
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        ];
    }

    protected $dates = ['deleted_at'];

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if the user is an admin.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user is an operator.
     *
     * @return bool
     */
    public function isHR(): bool
    {
        return $this->role === 'HR';
    }

    /**
     * Check if the user is a staff.
     *
     * @return bool
     */
    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    /**
     * Check if the user is a staff.
     *
     * @return bool
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
        if (!$this->foto) {
            return null;
        }
        
        return \Illuminate\Support\Facades\Storage::disk('supabase')->url($this->foto);
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
