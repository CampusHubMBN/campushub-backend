<?php
// app/Http/Controllers/MessageController.php
namespace App\Http\Controllers;

use App\Traits\PublishesRedisEvents;
use App\Enums\RealtimeEvent;
use App\Models\Message;

class MessageController extends Controller
{
    use PublishesRedisEvents;

    public function store(Request $request)
    {
        $message = Message::create([
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content'     => $request->content,
        ]);

        $message->load('sender');

        // ← Notifier le destinataire
        $this->publishEvent(RealtimeEvent::MESSAGE_CREATED, [
            'userId'     => $message->receiver_id,   // destinataire
            'senderId'   => $message->sender_id,
            'senderName' => $message->sender->name,
            'messageId'  => $message->id,
            'title'      => "Nouveau message de {$message->sender->name}",
            'body'       => mb_substr($message->content, 0, 100),
        ]);

        return response()->json($message, 201);
    }
}