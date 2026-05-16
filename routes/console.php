<?php

use App\Jobs\CheckMonitorJob;
use App\Models\Monitor;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    Monitor::all()->each(function (Monitor $monitor) {
        $lastChecked = $monitor->last_checked_at;

        $isDue = $lastChecked === null
            || $lastChecked->addMinutes($monitor->check_interval)->isPast();
        if ($isDue) {
            CheckMonitorJob::dispatch($monitor);
        }
    });
})->everyMinute();
