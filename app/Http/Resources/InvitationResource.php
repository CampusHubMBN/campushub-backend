<?php
// app/Http/Resources/InvitationResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvitationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'role' => $this->role,
            
            // Token seulement pour admin
            'token' => $this->when($request->user()?->role === 'admin', $this->token),
            'invitation_url' => $this->when($request->user()?->role === 'admin', $this->getInvitationUrl()),
            
            'expires_at' => $this->expires_at,
            'used' => $this->used,
            'used_at' => $this->used_at,
            'is_expired' => $this->isExpired(),
            'is_valid' => $this->isValid(),
            
            'invited_by' => $this->whenLoaded('invitedBy', function () {
                return [
                    'id' => $this->invitedBy->id,
                    'name' => $this->invitedBy->name,
                    'email' => $this->invitedBy->email,
                ];
            }),
            
            'created_at' => $this->created_at,
        ];
    }
}