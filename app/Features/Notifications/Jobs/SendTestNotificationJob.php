<?php

namespace App\Features\Notifications\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendTestNotificationJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly string $message)
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        Log::info('Notificación de prueba: '.$this->message);
    }
}
