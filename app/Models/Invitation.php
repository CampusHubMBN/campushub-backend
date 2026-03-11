<?php
// app/Models/Invitation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'email',
        'role',
        'token',
        'expires_at',
        'used',
        'used_at',
        'invited_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
            'used' => 'boolean',
        ];
    }

    /**
     * Générer token et expiration automatiquement
     */
    protected static function booted()
    {
        static::creating(function ($invitation) {
            $invitation->token = Str::random(64);
            $invitation->expires_at = now()->addDays(7);
        });
    }

    /**
     * Relation: invité par quel user
     */
    public function invitedBy()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Vérifier si expiré
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Vérifier si utilisé
     */
    public function isUsed(): bool
    {
        return (bool) $this->used;
    }

    /**
     * Marquer comme utilisé
     */
    public function markAsUsed(): void
    {
        $this->update([
            'used' => true,
            'used_at' => now(),
        ]);
    }

    /**
     * Vérifier validité
     */
    public function isValid(): bool
    {
        return !$this->isExpired() && !$this->isUsed();
    }

    /**
     * Générer URL d'invitation
     */
    public function getInvitationUrl(): string
    {
        return config('app.frontend_url') . '/register?token=' . $this->token;
    }
}