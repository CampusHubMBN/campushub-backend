<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMail extends Command
{
    protected $signature   = 'mail:test {email}';
    protected $description = 'Send a test email via Resend';

    public function handle(): void
    {
        $to = $this->argument('email');

        Mail::raw("Ceci est un test d'envoi depuis CampusHub via Resend.", function ($m) use ($to) {
            $m->to($to)->subject('Test CampusHub — Resend');
        });

        $this->info("Email envoyé à {$to}");
    }
}
