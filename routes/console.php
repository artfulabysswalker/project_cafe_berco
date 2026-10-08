<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Maintenance
|--------------------------------------------------------------------------
|
| Setiap sesi browser membuat user guest sendiri, jadi tabel users akan
| bertambah terus. Bersihkan yang sudah tidak relevan setiap malam.
|
*/

Schedule::command('guests:prune --days=14')
    ->dailyAt('02:30')
    ->withoutOverlapping();
