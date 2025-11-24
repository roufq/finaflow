<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('financial:sync-news')
    ->twiceDaily(5, 17)
    ->withoutOverlapping()
    ->description('Pull curated and API based financial news for the education hub');

Schedule::command('bank:sync-integrations')
    ->hourly()
    ->withoutOverlapping()
    ->description('Refresh transactions and balances for active bank integrations');
