<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command('app:auto-cancel-appointments')
    ->daily();

Schedule::command('app:auto-reschedule-appointments')
    ->daily();

Schedule::command(
    'appointments:generate-recurrence'
)->daily();
