<?php
// app/Http/Resources/EventResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description,
            'location'       => $this->location,
            'start_date'     => $this->start_date?->toISOString(),
            'end_date'       => $this->end_date?->toISOString(),
            'capacity'       => $this->capacity,
            'cover_image'    => $this->cover_image,
            'event_type'     => $this->event_type,
            'target_roles'   => $this->target_roles,
            'published_at'   => $this->published_at?->toISOString(),
            'attendees_count' => $this->attendees_count ?? 0,
            'is_full'        => $this->capacity ? ($this->attendees_count ?? 0) >= $this->capacity : false,

            'organizer' => $this->whenLoaded('organizer', fn() => [
                'id'     => $this->organizer->id,
                'name'   => $this->organizer->name,
            ]),

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
