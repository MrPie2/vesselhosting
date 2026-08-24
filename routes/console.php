<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:prune-batches')->daily();
Schedule::call(function () {
    // Add expiry notification jobs here:
    // Domain::whereBetween('expiry_date',[now(),now()->addDays(30)])->...
})->dailyAt('02:00')->name('domain-expiry-scan')->withoutOverlapping();
