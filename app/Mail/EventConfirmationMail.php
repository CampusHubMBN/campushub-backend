<?php
// app/Mail/EventConfirmationMail.php

namespace App\Mail;

use App\Models\CampusEvent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly CampusEvent $event,
        public readonly User $attendee,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Inscription confirmée : {$this->event->title}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.event_confirmation',
            with: [
                'eventTitle'    => $this->event->title,
                'eventDate'     => $this->event->start_date->format('d/m/Y à H:i'),
                'eventLocation' => $this->event->location,
                'eventType'     => $this->getTypeLabel($this->event->event_type),
                'userName'      => $this->attendee->name,
                'eventUrl'      => config('app.frontend_url') . '/events/' . $this->event->id,
            ],
        );
    }

    private function getTypeLabel(string $type): string
    {
        return match($type) {
            'workshop'    => 'Atelier',
            'conference'  => 'Conférence',
            'networking'  => 'Networking',
            'sports'      => 'Sport',
            default       => 'Événement',
        };
    }
}
