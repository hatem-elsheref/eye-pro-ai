<?php

use App\Services\AdminService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (AdminService $adminService) {
    $this->comment(Inspiring::quote());

    $adminService->approveUser(\App\Models\User::query()->find(1));
})->purpose('Display an inspiring quote');

// Schedule cleanup of orphaned chunks (runs daily at 2 AM)
Schedule::command('chunks:cleanup --force --older-than=4')
    ->dailyAt('02:00')
    ->description('Clean up orphaned chunks and expired cache keys');

// Schedule check for stale matches (runs daily at 3 AM)
Schedule::command('matches:check-stale')
    ->dailyAt('03:00')
    ->description('Check for matches older than 24 hours still processing and mark as failed');
