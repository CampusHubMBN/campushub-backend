<?php
// app/Http/Controllers/EventController.php
namespace App\Http\Controllers;

use App\Traits\PublishesRedisEvents;
use App\Enums\RealtimeEvent;
use App\Models\CampusEvent;

class EventController extends Controller
{
    use PublishesRedisEvents;

    public function publish(CampusEvent $event)
    {
        $event->update(['published_at' => now()]);
        $event->load('organizer');

        // ← Notifier tous les users (payload sans userId = broadcast)
        $this->publishEvent(RealtimeEvent::EVENT_PUBLISHED, [
            'eventId'       => $event->id,
            'title'         => "Nouvel événement : {$event->title}",
            'body'          => $event->description,
            'location'      => $event->location,
            'startDate'     => $event->start_date,
            'organizerName' => $event->organizer->name,
        ]);

        return response()->json($event);
    }
}