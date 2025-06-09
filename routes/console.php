<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote');


Schedule::command('app:restore-system')
    ->everyThirtyMinutes()
    ->onFailure(function () {
        // Handle failure, e.g., log an error or send a notification
    });