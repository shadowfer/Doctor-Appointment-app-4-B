<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ejecutar el envío de recordatorios todos los días a las 8:00 AM
Schedule::command('appointments:send-reminders')->dailyAt('08:00');

// Enviar reporte diario de citas al administrador y doctores a las 8:00 AM
Schedule::command('report:daily-appointments')->dailyAt('08:00');
