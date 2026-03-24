<?php
// app/Models/EventAttendance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EventAttendance extends Model
{
    use HasUuids;

    protected $table = 'event_attendances';

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'reminder_sent',
    ];

    protected $casts = [
        'reminder_sent' => 'boolean',
    ];

    public function event()
    {
        return $this->belongsTo(CampusEvent::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
