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
        // $userValidated = $request->validate([
        //     'name' => 'sometimes|string|max:255',
        // ]);

        // if (!empty($userValidated)) {
        //     $user->update($userValidated);
        // }

        // Validation
        $validated = $request->validate([
            'bio' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'linkedin_url' => 'nullable|url|max:255',
            'github_url' => 'nullable|url|max:255',
            'website_url' => 'nullable|url|max:255',
            'cv_url' => 'nullable|url|max:255',
            'skills' => 'nullable|array',
            'skills.*' => 'string|max:50',
            'languages' => 'nullable|array',
            'languages.*.language' => 'required|string',
            'languages.*.level' => 'required|string',
            'program' => 'nullable|string|max:100',
            'year' => 'nullable|integer|min:1|max:5',
            'graduation_year' => 'nullable|integer|min:2020|max:2030',
            'specialization' => 'nullable|string|max:100',
            'campus' => 'nullable|string|max:100',
        ]);

        if ($user->info) {
            $user->info->update($validated);
            
            // Recalculer profile_completion
            $profileCompletion = $user->info->calculateProfileCompletion();
            $user->info->update(['profile_completion' => $profileCompletion]);
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

         // Upload fichier
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            
            // Stocker dans storage/app/public/avatars
            $path = $file->store('avatars', 'public');
            
            // URL publique
            $url = asset('storage/' . $path);
            
            // Update user_info
            if ($user->info) {
                // Supprimer ancien avatar si existe
                if ($user->info->avatar_url) {
                    $oldPath = str_replace(asset('storage/'), '', $user->info->avatar_url);
                    \Storage::disk('public')->delete($oldPath);
                }
                
                $user->info->update(['avatar_url' => $url]);
            }

            return response()->json([
                'message' => 'Avatar uploadé avec succès',
                'avatar_url' => $url,
            ]);
        }
        
        return response()->json(['message' => 'Aucun fichier trouvé'], 400);
    }
}