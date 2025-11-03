<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
// Schedule cleanup of orphaned chunks (runs daily at 2 AM)
Schedule::command('chunks:cleanup --force --older-than=12')
    ->dailyAt('02:00')
    ->description('Clean up orphaned chunks and expired cache keys');
*/
/*
// Schedule check for stale matches (runs daily at 3 AM)
Schedule::command('matches:check-stale')
    ->dailyAt('03:00')
    ->description('Check for matches older than 24 hours still processing and mark as failed');
*/
