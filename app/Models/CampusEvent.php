<?php
// app/Models/CampusEvent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampusEvent extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'campus_events';

    protected $fillable = [
        'title',
        'description',
        'location',
        'start_date',
        'end_date',
        'capacity',
        'cover_image',
        'event_type',
        'target_roles',
        'organizer_id',
        'published_at',
    ];

    protected $casts = [
        'start_date'   => 'datetime',
        'end_date'     => 'datetime',
        'published_at' => 'datetime',
        'capacity'     => 'integer',
        'target_roles' => 'array',
    ];

    // ── Relations ────────────────────────────────────────────────────

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function attendances()
    {
        return $this->hasMany(EventAttendance::class, 'event_id');
    }

    public function registeredAttendances()
    {
        return $this->hasMany(EventAttendance::class, 'event_id')->where('status', 'registered');
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->published()->where('start_date', '>=', now());
    }

    // ── Methods ──────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at <= now();
    }

    public function isFull(): bool
    {
        if (!$this->capacity) return false;
        return $this->registeredAttendances()->count() >= $this->capacity;
    }

    public function isUserRegistered(string $userId): bool
    {
        return $this->attendances()
            ->where('user_id', $userId)
            ->where('status', 'registered')
            ->exists();
    }

    public function attendeesCount(): int
    {
        return $this->registeredAttendances()->count();
    }
}
