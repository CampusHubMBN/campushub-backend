<?php
// routes/api.php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

// ========== PUBLIC ROUTES ==========

// Vérifier invitation (public)
Route::post('/invitations/verify', [InvitationController::class, 'verify']);

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

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
});