<?php
// app/Mail/InvitationMail.php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation;

    public function __construct(Invitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation à rejoindre CampusHub',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invitation',
            with: [
                'invitationUrl' => $this->invitation->getInvitationUrl(),
                'roleName' => $this->getRoleName($this->invitation->role),
                'expiresAt' => $this->invitation->expires_at->format('d/m/Y à H:i'),
            ],
        );
    }

    private function getRoleName(string $role): string
    {
        $roles = [
            'student' => 'Étudiant',
            'alumni' => 'Alumni',
            'bde_member' => 'Membre du BDE',
            'pedagogical' => 'Équipe Pédagogique',
            'company' => 'Entreprise',
        ];

        return $roles[$role] ?? $role;
    }
}