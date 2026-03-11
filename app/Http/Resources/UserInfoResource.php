<?php
// app/Http/Resources/UserInfoResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'avatar_url' => $this->avatar_url,
            'bio' => $this->bio,
            'phone' => $this->phone,
            'linkedin_url' => $this->linkedin_url,
            'github_url' => $this->github_url,
            'website_url' => $this->website_url,
            'cv_url' => $this->cv_url,
            'skills' => $this->skills,
            'languages' => $this->languages,
            'program' => $this->program,
            'year' => $this->year,
            'graduation_year' => $this->graduation_year,
            'specialization' => $this->specialization,
            'campus' => $this->campus,
            'company_id' => $this->company_id,
            'reputation_points' => $this->reputation_points,
            'level' => $this->level,
            'profile_completion' => $this->profile_completion,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}