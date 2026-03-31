<?php
// app/Http/Controllers/JobController.php

namespace App\Http\Controllers;

use App\Http\Resources\JobResource;
use App\Models\Job;
use Illuminate\Http\Request;

class JobController extends Controller
{
    /**
     * List all active jobs (public)
     */
    public function index(Request $request)
    {
        $query = Job::with(['company', 'postedBy'])
            ->active()
            ->latest('published_at');

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('location_type')) {
            $query->where('location_type', $request->location_type);
        }

        if ($request->filled('source_type')) {
            $query->where('source_type', $request->source_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%");
            });
        }

        $jobs = $query->paginate(20);

        return JobResource::collection($jobs);
    }

    /**
     * Show single job (public)
     */
    public function show($id)
    {
        $job = Job::with(['company', 'postedBy'])->findOrFail($id);

        // Increment views
        $job->incrementViews();

        return new JobResource($job);
    }

    /**
     * Get recruiter jobs:
     *   - admin  → all jobs across all companies
     *   - others → only jobs posted by the current user
     */
    public function myPostedJobs(Request $request)
    {
        $user  = $request->user();
        $query = Job::with(['company', 'postedBy'])
            ->withCount(['applications', 'applications as pending_count' => function ($q) {
                $q->where('status', 'pending');
            }])
            ->latest();

        if ($user->role !== 'admin') {
            $query->where('posted_by', $user->id);
        }

        return JobResource::collection($query->paginate(20));
    }

    /**
     * Create job (admin/company/pedagogical only)
     */
    public function store(Request $request)
    {
        // Authorization
        if (!in_array($request->user()->role, ['admin', 'company', 'pedagogical'])) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'type' => 'required|in:internship,apprenticeship,cdd,cdi,freelance,student_job',
            'location_type' => 'required|in:onsite,remote,hybrid',
            'location_city' => 'nullable|string|max:100',
            'location_country' => 'nullable|string|max:100',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0',
            'salary_period' => 'nullable|in:hourly,monthly,yearly',
            'duration_months' => 'nullable|integer|min:1',
            'hours_per_week' => 'nullable|integer|min:1|max:21',
            'start_date' => 'nullable|date',
            'application_url' => 'nullable|url',
            'application_email' => 'nullable|email',
            'application_deadline' => 'nullable|date|after:today',
            'source_type' => 'required|in:internal,external',
            'company_name' => 'required_if:source_type,external|string|max:255',
            'external_url' => 'nullable|url',
            'company_id' => 'required_if:source_type,internal|exists:companies,id',
            'status' => 'nullable|in:draft,published',
        ], [
            'hours_per_week.max' => 'Un job étudiant ne peut pas dépasser 21h par semaine.',
        ]);

        $validated['posted_by'] = $request->user()->id;

        if (isset($validated['status']) && $validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $job = Job::create($validated);

        return new JobResource($job->load(['company', 'postedBy']));
    }

    /**
     * Update job (admin/company/creator only)
     */
    public function update(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        // Authorization
        $user = $request->user();
        if ($user->role !== 'admin' && $job->posted_by !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'type' => 'sometimes|in:internship,apprenticeship,cdd,cdi,freelance,student_job',
            'location_type' => 'sometimes|in:onsite,remote,hybrid',
            'location_city' => 'nullable|string|max:100',
            'salary_min' => 'nullable|integer|min:0',
            'salary_max' => 'nullable|integer|min:0',
            'hours_per_week' => 'nullable|integer|min:1|max:21',
            'application_deadline' => 'nullable|date',
            'status' => 'sometimes|in:draft,published,closed,filled',
        ]);

        // If publishing, set published_at
        if (isset($validated['status']) && $validated['status'] === 'published' && !$job->published_at) {
            $validated['published_at'] = now();
        }

        $job->update($validated);

        return new JobResource($job->load(['company', 'postedBy']));
    }

    /**
     * Delete job (admin/creator only)
     */
    public function destroy(Request $request, $id)
    {
        $job = Job::findOrFail($id);

        // Authorization
        $user = $request->user();
        if ($user->role !== 'admin' && $job->posted_by !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Offre supprimée avec succès']);
    }
}