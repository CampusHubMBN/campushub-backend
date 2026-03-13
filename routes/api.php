<?php
// routes/api.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JopApplicarionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// ========== PUBLIC ROUTES ==========

// Vérifier invitation (public)
Route::post('/invitations/verify', [InvitationController::class, 'verify']);

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Routes publics jobs
Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);

// ========== PROTECTED ROUTES ==========

Route::middleware(['auth:sanctum'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'user']);
    
    // Invitations (Admin only - vérification dans controller)
    Route::get('/invitations', [InvitationController::class, 'index']);
    Route::post('/invitations', [InvitationController::class, 'store']);
    Route::post('/invitations/{id}/resend', [InvitationController::class, 'resend']);
    Route::delete('/invitations/{id}', [InvitationController::class, 'destroy']);
    // User
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::patch('/users/{id}', [UserController::class, 'update']);
    Route::post('/users/{id}/avatar', [UserController::class, 'uploadAvatar']);

    // Jobs - CRUD (admin/company)
    Route::post('/jobs', [JobController::class, 'store']);
    Route::patch('/jobs/{id}', [JobController::class, 'update']);
    Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
    
    // Job Applications
    Route::post('/jobs/{id}/apply', [JobApplicationController::class, 'apply']);
    Route::get('/my-applications', [JobApplicationController::class, 'myApplications']);
    Route::get('/applications/{id}', [JobApplicationController::class, 'show']);
    Route::get('/jobs/{id}/applications', [JobApplicationController::class, 'jobApplications']);
    Route::patch('/applications/{id}/status', [JobApplicationController::class, 'updateStatus']);
    Route::post('/applications/{id}/withdraw', [JobApplicationController::class, 'withdraw']);
    Route::delete('/applications/{id}', [JobApplicationController::class, 'destroy']);
});