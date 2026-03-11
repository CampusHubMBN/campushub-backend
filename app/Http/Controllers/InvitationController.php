<?php
// app/Http/Controllers/InvitationController.php

namespace App\Http\Controllers;

use App\Http\Resources\InvitationResource;
use App\Mail\InvitationMail;
use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    /**
     * Liste invitations (Admin only)
     */
    public function index(Request $request)
    {
        // Vérifier admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $invitations = Invitation::with('invitedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return InvitationResource::collection($invitations);
    }

    /**
     * Créer invitation (Admin only)
     */
    public function store(Request $request)
    {
        // Vérifier admin
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        // Validation
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                'unique:users,email',
                'unique:invitations,email',
            ],
            'role' => [
                'required',
                'in:student,alumni,bde_member,pedagogical,company',
            ],
        ], [
            'email.unique' => 'Cet email est déjà inscrit ou a déjà une invitation en attente.',
        ]);

        // Créer invitation
        $invitation = Invitation::create([
            'email' => $validated['email'],
            'role' => $validated['role'],
            'invited_by' => $request->user()->id,
        ]);

        // Envoyer email (on va créer le mail juste après)
        try {
            Mail::to($invitation->email)->send(new InvitationMail($invitation));
        } catch (\Exception $e) {
            // Log error mais ne pas fail la création
            \Log::error('Erreur envoi email invitation: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Invitation envoyée avec succès',
            'invitation' => new InvitationResource($invitation),
        ], 201);
    }

    /**
     * Vérifier token (Public)
     */
    public function verify(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $invitation = Invitation::where('token', $validated['token'])->first();

        if (!$invitation) {
            throw ValidationException::withMessages([
                'token' => ['Invitation invalide.'],
            ]);
        }

        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'token' => ['Cette invitation a expiré.'],
            ]);
        }

        if ($invitation->isUsed()) {
            throw ValidationException::withMessages([
                'token' => ['Cette invitation a déjà été utilisée.'],
            ]);
        }

        return response()->json([
            'email' => $invitation->email,
            'role' => $invitation->role,
        ]);
    }

    /**
     * Renvoyer invitation (Admin only)
     */
    public function resend(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $invitation = Invitation::findOrFail($id);

        if ($invitation->isUsed()) {
            return response()->json([
                'message' => 'Cette invitation a déjà été utilisée',
            ], 422);
        }

        // Prolonger expiration
        $invitation->update([
            'expires_at' => now()->addDays(7),
        ]);

        // Renvoyer email
        try {
            Mail::to($invitation->email)->send(new InvitationMail($invitation));
        } catch (\Exception $e) {
            \Log::error('Erreur renvoi email invitation: ' . $e->getMessage());
            return response()->json([
                'message' => 'Erreur lors de l\'envoi de l\'email',
            ], 500);
        }

        return response()->json([
            'message' => 'Invitation renvoyée avec succès',
            'invitation' => new InvitationResource($invitation),
        ]);
    }

    /**
     * Supprimer invitation (Admin only)
     */
    public function destroy(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $invitation = Invitation::findOrFail($id);
        $invitation->delete();

        return response()->json([
            'message' => 'Invitation supprimée',
        ]);
    }
}