<?php

namespace App\Traits;

use App\Services\AuditLogService;

/**
 * Attach this trait to any Eloquent model to automatically log
 * created / updated / deleted / restored / forceDeleted events.
 *
 * Usage: use LogsAudit; (alongside HasFactory, SoftDeletes, etc.)
 */
trait LogsAudit
{
    public static function bootLogsAudit(): void
    {
        static::created(function ($model) {
            AuditLogService::logModelEvent(
                'created',
                $model,
                class_basename($model).' #'.$model->getKey().' created'
            );
        });

        static::updated(function ($model) {
            AuditLogService::logModelEvent(
                'updated',
                $model,
                class_basename($model).' #'.$model->getKey().' updated'
            );
        });

        static::deleted(function ($model) {
            AuditLogService::log(
                'deleted',
                $model,
                class_basename($model).' #'.$model->getKey().' moved to trash'
            );
        });

        // SoftDeletes restore
        if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive(static::class))) {
            static::restored(function ($model) {
                AuditLogService::log(
                    'restored',
                    $model,
                    class_basename($model).' #'.$model->getKey().' restored from trash'
                );
            });

            static::forceDeleted(function ($model) {
                AuditLogService::log(
                    'force_deleted',
                    $model,
                    class_basename($model).' #'.$model->getKey().' permanently deleted'
                );
            });
        }
    }
}
