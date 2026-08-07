<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Bonus;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Position;
use App\Models\User;
use Illuminate\Console\Command;

class CleanUpTrashCommand extends Command
{
    protected $signature = 'app:cleanup-trash
                            {--trash-days=90 : Permanently delete soft-deleted records older than this many days}
                            {--log-days=365  : Purge audit log entries older than this many days}
                            {--logs-only     : Only purge audit logs, skip trash cleanup}
                            {--trash-only    : Only clean trash, skip audit log purge}';

    protected $description = 'Permanently delete old trash items and prune old audit log entries';

    /**
     * Models that support soft-deletes and should be cleaned from trash.
     */
    private array $trashableModels = [
        User::class,
        Employee::class,
        Department::class,
        Position::class,
        Payroll::class,
        Bonus::class,
    ];

    public function handle(): int
    {
        $trashDays = (int) $this->option('trash-days');
        $logDays = (int) $this->option('log-days');
        $logsOnly = $this->option('logs-only');
        $trashOnly = $this->option('trash-only');

        $this->info('╔══════════════════════════════════════╗');
        $this->info('║       S-Payroll Cleanup Job          ║');
        $this->info('╚══════════════════════════════════════╝');
        $this->newLine();

        $totalTrash = 0;
        $totalLogs = 0;

        // ----------------------------------------------------------------
        // 1. Trash cleanup
        // ----------------------------------------------------------------
        if (! $logsOnly) {
            $cutoff = now()->subDays($trashDays);
            $this->info("🗑  Trash cleanup — records soft-deleted before {$cutoff->toDateString()} ({$trashDays}d)");

            foreach ($this->trashableModels as $modelClass) {
                $name = class_basename($modelClass);
                $count = $modelClass::onlyTrashed()
                    ->where('deleted_at', '<', $cutoff)
                    ->forceDelete();

                if ($count > 0) {
                    $this->line("   ✓ {$name}: {$count} record(s) permanently deleted");
                    $totalTrash += $count;
                } else {
                    $this->line("   – {$name}: nothing to delete");
                }
            }

            $this->newLine();
        }

        // ----------------------------------------------------------------
        // 2. Audit log pruning
        // ----------------------------------------------------------------
        if (! $trashOnly) {
            $logCutoff = now()->subDays($logDays);
            $this->info("📋  Audit log pruning — entries older than {$logCutoff->toDateString()} ({$logDays}d)");

            $totalLogs = AuditLog::where('created_at', '<', $logCutoff)->delete();

            if ($totalLogs > 0) {
                $this->line("   ✓ AuditLog: {$totalLogs} record(s) purged");
            } else {
                $this->line('   – AuditLog: nothing to purge');
            }

            $this->newLine();
        }

        // ----------------------------------------------------------------
        // Summary
        // ----------------------------------------------------------------
        $this->info('Summary:');
        if (! $logsOnly) {
            $this->line("  Trash records deleted : {$totalTrash}");
        }
        if (! $trashOnly) {
            $this->line("  Audit logs purged     : {$totalLogs}");
        }
        $this->info('Cleanup complete.');

        return self::SUCCESS;
    }
}
