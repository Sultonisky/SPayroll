<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Reset live demo data every day at midnight
Schedule::command('demo:reset --force')
    ->dailyAt('00:00')
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/demo-reset.log'));
