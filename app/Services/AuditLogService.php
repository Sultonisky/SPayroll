<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Fields that should never be stored in old_values / new_values.
     */
    private const HIDDEN_FIELDS = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Write an audit log entry.
     *
     * @param  string  $action        e.g. 'created', 'updated', 'login', 'export'
     * @param  Model|null  $model     The Eloquent model being acted on (optional)
     * @param  string|null  $description  Human-readable summary
     * @param  array|null  $oldValues  State before the change
     * @param  array|null  $newValues  State after the change
     */
    public static function log(
        string $action,
        ?Model $model = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id'        => Auth::id(),
            'action'         => $action,
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id'   => $model?->getKey(),
            'description'    => $description,
            'old_values'     => $oldValues ? self::sanitize($oldValues) : null,
            'new_values'     => $newValues ? self::sanitize($newValues) : null,
            'ip_address'     => Request::ip(),
            'user_agent'     => Request::userAgent(),
            'url'            => Request::fullUrl(),
            'method'         => Request::method(),
            'created_at'     => now(),
        ]);
    }

    /**
     * Convenience: log from an observer (auto-detect old/new from dirty model).
     */
    public static function logModelEvent(
        string $action,
        Model $model,
        ?string $description = null,
    ): AuditLog {
        $oldValues = null;
        $newValues = null;

        if ($action === 'updated') {
            $dirty    = $model->getDirty();
            $original = array_intersect_key($model->getOriginal(), $dirty);
            $oldValues = self::sanitize($original);
            $newValues = self::sanitize($dirty);
        } elseif ($action === 'created') {
            $newValues = self::sanitize($model->getAttributes());
        }

        return self::log($action, $model, $description, $oldValues, $newValues);
    }

    /**
     * Remove sensitive fields from attribute snapshots.
     */
    private static function sanitize(array $data): array
    {
        foreach (self::HIDDEN_FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = '***';
            }
        }

        return $data;
    }
}
