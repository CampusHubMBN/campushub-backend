<?php
// app/Traits/PublishesRedisEvents.php
namespace App\Traits;

use App\Enums\RealtimeEvent;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

trait PublishesRedisEvents
{
    protected function publishEvent(RealtimeEvent $type, array $payload): void
    {
        try {
            Redis::connection('realtime')->publish(
                'campushub:notifications',
                json_encode([
                    'type'    => $type->value,
                    'payload' => $payload,
                ])
            );
        } catch (\Exception $e) {
            Log::error("Redis publish error: {$e->getMessage()}");
        }
    }
}