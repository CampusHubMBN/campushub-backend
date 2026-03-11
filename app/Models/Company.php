<?php
// app/Models/Company.php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'siret',
        'logo_url',
        'website',
        'linkedin_url',
        'description',
        'industry',
        'size',
        'headquarters_street',
        'headquarters_city',
        'headquarters_postal_code',
        'headquarters_country',
        'is_partner',
        'is_verified',
        'verified_at',
        'jobs_posted',
        'active_jobs',
    ];

    protected function casts(): array
    {
        return [
            'is_partner' => 'boolean',
            'is_verified' => 'boolean',
            'verified_at' => 'datetime',
            'jobs_posted' => 'integer',
            'active_jobs' => 'integer',
        ];
    }

    /**
     * Users de cette company
     */
    public function users()
    {
        return $this->hasMany(UserInfo::class);
    }
}