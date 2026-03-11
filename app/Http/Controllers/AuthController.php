<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register avec token d'invitation
     */
    public function register(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'invitation_token' => 'required|string',
        ]);

        // Vérifier invitation
        $invitation = Invitation::where('token', $validated['invitation_token'])->first();

        if (!$invitation) {
            throw ValidationException::withMessages([
                'invitation_token' => ['Token d\'invitation invalide.'],
            ]);
        }

        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'invitation_token' => ['Cette invitation a expiré.'],
            ]);
        }

        if ($invitation->isUsed()) {
            throw ValidationException::withMessages([
                'invitation_token' => ['Cette invitation a déjà été utilisée.'],
            ]);
        }

        if ($invitation->email !== $validated['email']) {
            throw ValidationException::withMessages([
                'email' => ['L\'email ne correspond pas à l\'invitation.'],
            ]);
        }

        // Créer user avec rôle de l'invitation
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $invitation->role,
        ]);

        // UserInfo créé automatiquement via booted()

        // Marquer invitation comme utilisée
        $invitation->markAsUsed();

        // Auto-login
        Auth::login($user);
        $request->session()->regenerate();

        // Eager load info
        $user->load('info');

        return response()->json([
            'message' => 'Compte créé avec succès',
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Régénérer session (sécurité)
        $request->session()->regenerate();

        // Update last_login_at
        $user = Auth::user();
        $user->update(['last_login_at' => now()]);

        // Eager load info
        $user->load('info');

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * Get current user
     */
    public function user(Request $request)
    {
        // Eager load info
        $user = $request->user()->load('info');

        return new UserResource($user);
    }
}