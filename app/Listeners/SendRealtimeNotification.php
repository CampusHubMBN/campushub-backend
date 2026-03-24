<?php
// app/Listeners/SendRealtimeNotification.php

namespace App\Listeners;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class SendRealtimeNotification
{
    /**
     * Handle the event.
     * Appelé via Event::dispatch() ou automatiquement par Laravel event system.
     */
    public function handle(object $event): void
    {
        // Ce listener est un placeholder — la logique de publication
        // Redis est gérée directement dans les controllers via le trait
        // PublishesRedisEvents. Ce fichier ne fait rien pour l'instant.
    }
}