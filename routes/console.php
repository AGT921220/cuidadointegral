<?php

// El contenedor scheduler ejecuta `php artisan schedule:work`.
// Los jobs pesados se despachan a Redis y Horizon los procesa.
//
// use Illuminate\Support\Facades\Schedule;
// Schedule::job(new \App\Features\Notifications\Jobs\SendTestNotificationJob('ping'))
//     ->dailyAt('02:00');
