<?php
// app/Http/Resources/JobResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            
            'type' => $this->type,
            'location_type' => $this->location_type,
            'location_city' => $this->location_city,
            'location_country' => $this->location_country,
            
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'salary_period' => $this->salary_period,
            'salary_currency' => $this->salary_currency,
            
            'duration_months' => $this->duration_months,
            'start_date' => $this->start_date?->format('Y-m-d'),
            
            'application_url' => $this->application_url,
            'application_email' => $this->application_email,
            'application_deadline' => $this->application_deadline?->format('Y-m-d'),
            
            'source_type' => $this->source_type,
            'company_name' => $this->company_name,
            'external_url' => $this->external_url,
            
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'posted_by' => UserResource::make($this->whenLoaded('postedBy')),
            
            'views_count' => $this->views_count,
            'applications_count' => $this->applications_count,
            
            'status' => $this->status,
            'published_at' => $this->published_at?->toISOString(),
            'closed_at' => $this->closed_at?->toISOString(),
            
            'is_active' => $this->isActive(),
            'can_apply' => $this->canApply(),
            'has_applied' => $this->when(
                $request->user(),
                fn() => $this->hasUserApplied($request->user()->id)
            ),
            
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}