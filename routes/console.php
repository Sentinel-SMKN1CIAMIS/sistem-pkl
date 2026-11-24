<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune')->daily();
Schedule::command('pkl:send-reminders')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('pkl:smart-attendance --action=auto')->everyFifteenMinutes()->withoutOverlapping();
Schedule::command('pkl:smart-attendance --action=in')->weekdays()->at('08:00');
Schedule::command('pkl:smart-attendance --action=out')->weekdays()->at('17:00');

