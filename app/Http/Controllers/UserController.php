<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /**
     * Afficher un profil utilisateur
     */
    public function show($id)
    {
        $user = User::with('info')->findOrFail($id);
        
        return new UserResource($user);
    }

    /**
     * Mettre à jour le profil
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Vérifier autorisation
        if ($request->user()->id !== $user->id && $request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Update user (auth fields)
        $userValidated = $request->validate([
            'name' => 'sometimes|string|max:255',
        ]);

        if (!empty($userValidated)) {
            $user->update($userValidated);
        }

        // Update user_info (profil fields)
        $infoValidated = $request->validate([
            'bio' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'skills' => 'nullable|array',
            'skills.*' => 'string',
            'languages' => 'nullable|array',
            'program' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1|max:5',
            'campus' => 'nullable|string|max:100',
        ]);

        if (!empty($infoValidated)) {
            $user->info()->update($infoValidated);
            
            // Recalculer profile_completion
            $completion = $user->info->calculateProfileCompletion();
            $user->info()->update(['profile_completion' => $completion]);
        }

        // Reload
        $user->load('info');

        return new UserResource($user);
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($request->user()->id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Supprimer ancien avatar
        if ($user->info->avatar_url) {
            Storage::disk('public')->delete($user->info->avatar_url);
        }

        // Stocker nouveau
        $path = $request->file('avatar')->store('avatars', 'public');

        // Update user_info
        $user->info()->update(['avatar_url' => $path]);

        // Recalculer completion
        $completion = $user->info->calculateProfileCompletion();
        $user->info()->update(['profile_completion' => $completion]);

        $user->load('info');

        return new UserResource($user);
    }
}