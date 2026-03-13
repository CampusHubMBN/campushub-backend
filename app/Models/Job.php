<?php
// app/Models/Job.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'benefits',
        'type',
        'location_type',
        'location_city',
        'location_country',
        'salary_min',
        'salary_max',
        'salary_period',
        'salary_currency',
        'duration_months',
        'start_date',
        'application_url',
        'application_email',
        'application_deadline',
        'source_type',
        'company_name',
        'external_url',
        'company_id',
        'posted_by',
        'views_count',
        'applications_count',
        'status',
        'published_at',
        'closed_at',
    ];

    protected $casts = [
        'salary_min' => 'integer',
        'salary_max' => 'integer',
        'duration_months' => 'integer',
        'views_count' => 'integer',
        'applications_count' => 'integer',
        'start_date' => 'date',
        'application_deadline' => 'date',
        'published_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    // Relations
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function postedBy()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    public function scopeActive($query)
    {
        return $query->published()
                     ->where('status', '!=', 'closed')
                     ->where('status', '!=', 'filled')
                     ->where(function ($q) {
                         $q->whereNull('application_deadline')
                           ->orWhere('application_deadline', '>=', now());
                     });
    }

    public function scopeInternal($query)
    {
        return $query->where('source_type', 'internal');
    }

    public function scopeExternal($query)
    {
        return $query->where('source_type', 'external');
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function incrementApplications()
    {
        $this->increment('applications_count');
    }

    public function isActive(): bool
    {
        return $this->status === 'published' 
            && $this->published_at <= now()
            && !in_array($this->status, ['closed', 'filled'])
            && (!$this->application_deadline || $this->application_deadline >= now());
    }

    public function canApply(): bool
    {
        return $this->isActive() && $this->source_type === 'internal';
    }

    public function hasUserApplied(string $userId): bool
    {
        return $this->applications()->where('user_id', $userId)->exists();
    }
}