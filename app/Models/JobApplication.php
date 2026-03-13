<?php
// app/Models/JobApplication.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'cv_url',
        'additional_documents',
        'status',
        'notes',
        'reviewed_at',
        'interview_at',
        'responded_at',
    ];

    protected $casts = [
        'additional_documents' => 'array',
        'reviewed_at' => 'datetime',
        'interview_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    // Relations
    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->whereIn('status', ['reviewed', 'shortlisted', 'interview', 'accepted', 'rejected']);
    }

    // Methods
    public function markAsReviewed()
    {
        $this->update([
            'status' => 'reviewed',
            'reviewed_at' => now(),
        ]);
    }

    public function markAsShortlisted()
    {
        $this->update([
            'status' => 'shortlisted',
            'reviewed_at' => $this->reviewed_at ?? now(),
        ]);
    }

    public function scheduleInterview(\DateTime $date)
    {
        $this->update([
            'status' => 'interview',
            'interview_at' => $date,
        ]);
    }

    public function accept()
    {
        $this->update([
            'status' => 'accepted',
            'responded_at' => now(),
        ]);
    }

    public function reject()
    {
        $this->update([
            'status' => 'rejected',
            'responded_at' => now(),
        ]);
    }
}