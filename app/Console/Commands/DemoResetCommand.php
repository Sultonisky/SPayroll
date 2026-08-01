<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DemoResetCommand extends Command
{
    protected $signature = 'demo:reset {--force : Skip confirmation prompt}';

    protected $description = 'Reset the live demo environment: wipe all data and re-seed fresh data.';

    private array $protectedEmails = [
        'admin@demo.scroll.com',
        'hr@demo.scroll.com',
        'manager@demo.scroll.com',
        'staff@demo.scroll.com',
    ];

    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will wipe all demo data and re-seed. Continue?')) {
                $this->info('Aborted.');

                return self::SUCCESS;
            }
        }

        $this->info('Starting demo reset...');

        // 1. Fresh migrate + seed (wipes everything and re-seeds)
        //    The seeder already creates demo users with is_demo = true
        $this->info('Running fresh migration and seeder...');
        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $this->line(Artisan::output());

        $this->info('');
        $this->info('Demo reset complete. Demo accounts (password: demo12345):');
        $this->line('  admin@demo.scroll.com   → Admin');
        $this->line('  hr@demo.scroll.com      → HR');
        $this->line('  manager@demo.scroll.com → Manager');
        $this->line('  staff@demo.scroll.com   → Staff');

        return self::SUCCESS;
    }
}
