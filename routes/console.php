<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Schedule cleanup of expired sessions every 5 minutes
Schedule::command('sessions:cleanup-expired')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();
