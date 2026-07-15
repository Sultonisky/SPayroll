<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupTrashCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-trash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Permanently delete items in trash that have been soft-deleted for more than 90 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $models = [
            \App\Models\User::class,
        ];

        $days = 90;
        $cutoffDate = now()->subDays($days);
        $totalDeleted = 0;

        $this->info("Cleaning up trash items older than $days days (before $cutoffDate)...");

        foreach ($models as $modelClass) {
            $modelName = class_basename($modelClass);
            $count = $modelClass::onlyTrashed()
                ->where('deleted_at', '<', $cutoffDate)
                ->forceDelete();

            if ($count > 0) {
                $this->info("- Deleted $count items from $modelName");
                $totalDeleted += $count;
            }
        }

        $this->info("Cleanup finished. Total items permanently deleted: $totalDeleted");
    }
}
