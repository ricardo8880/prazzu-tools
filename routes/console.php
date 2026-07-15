<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('tools:purge-history')->dailyAt('02:30')->withoutOverlapping();

Schedule::command('analytics:run-scheduled-reports')->hourly()->withoutOverlapping();

Schedule::command('analytics:generate-insights --days=7')->dailyAt('03:15')->withoutOverlapping();

Schedule::command('analytics:prune --chunk='.(int) config('analytics.performance.prune_chunk_size', 1000))
    ->dailyAt('04:00')
    ->withoutOverlapping();
