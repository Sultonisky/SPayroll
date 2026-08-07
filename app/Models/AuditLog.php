<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;
    /**
     * Audit logs are append-only — never update or soft-delete them.
     */
    public $timestamps = false;

    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    // ----------------------------------------------------------------
    // Relationships
    // ----------------------------------------------------------------

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /**
     * Get a display-friendly label for the model type.
     */
    public function getAuditableNameAttribute(): string
    {
        if (! $this->auditable_type) {
            return '—';
        }

        return class_basename($this->auditable_type);
    }

    /**
     * Action badge colour mapping (for UI).
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'created'      => 'success',
            'updated'      => 'warning',
            'deleted'      => 'danger',
            'restored'     => 'info',
            'force_deleted' => 'dark',
            'login'        => 'primary',
            'logout'       => 'secondary',
            'login_failed' => 'danger',
            'export'       => 'info',
            'approved'     => 'success',
            'rejected'     => 'danger',
            'mark_paid'    => 'success',
            default        => 'secondary',
        };
    }

    /**
     * Action icon mapping (Font Awesome).
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'created'      => 'fa-plus-circle',
            'updated'      => 'fa-edit',
            'deleted'      => 'fa-trash',
            'restored'     => 'fa-undo',
            'force_deleted' => 'fa-times-circle',
            'login'        => 'fa-sign-in-alt',
            'logout'       => 'fa-sign-out-alt',
            'login_failed' => 'fa-exclamation-triangle',
            'export'       => 'fa-download',
            'approved'     => 'fa-check-circle',
            'rejected'     => 'fa-ban',
            'mark_paid'    => 'fa-money-bill-wave',
            default        => 'fa-circle',
        };
    }
}
