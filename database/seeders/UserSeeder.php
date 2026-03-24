<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ─────────────────────────────────────────────────────────────
        $admin = User::create([
            'name'               => 'Admin CampusHub',
            'email'              => 'admin@campushub.fr',
            'password'           => Hash::make('password'),
            'role'               => 'admin',
            'email_verified_at'  => now(),
        ]);
        $admin->info->update([
            'bio'                => 'Administrateur de la plateforme CampusHub',
            'profile_completion' => 50,
        ]);

        // ── Étudiant ──────────────────────────────────────────────────────────
        $student = User::create([
            'name'               => 'Jean Dupont',
            'email'              => 'jean.dupont@campushub.fr',
            'password'           => Hash::make('password'),
            'role'               => 'student',
            'email_verified_at'  => now(),
        ]);
        $student->info->update([
            'program'            => 'Master Informatique',
            'year'               => 2,
            'campus'             => 'Paris',
            'skills'             => ['PHP', 'Laravel', 'JavaScript', 'React'],
            'profile_completion' => 70,
        ]);

        // ── Alumni ────────────────────────────────────────────────────────────
        $alumni = User::create([
            'name'               => 'Marie Martin',
            'email'              => 'marie.martin@campushub.fr',
            'password'           => Hash::make('password'),
            'role'               => 'alumni',
            'email_verified_at'  => now(),
        ]);
        $alumni->info->update([
            'program'            => 'Master Informatique',
            'graduation_year'    => 2022,
            'campus'             => 'Lyon',
            'skills'             => ['Python', 'Django', 'Machine Learning'],
            'linkedin_url'       => 'https://linkedin.com/in/marie-martin',
            'profile_completion' => 85,
        ]);

        // ── BDE ───────────────────────────────────────────────────────────────
        $bde = User::create([
            'name'               => 'Belem Gloire',
            'email'              => 'belem@campushub.fr',
            'password'           => Hash::make('password'),
            'role'               => 'bde_member',
            'email_verified_at'  => now(),
        ]);
        $bde->info->update([
            'program'            => 'Master Informatique',
            'graduation_year'    => 2022,
            'campus'             => 'Lyon',
            'skills'             => ['Python', 'Django', 'Machine Learning'],
            'linkedin_url'       => 'https://linkedin.com/in/marie-martin',
            'profile_completion' => 85,
        ]);

        // ── Company recruiters ────────────────────────────────────────────────
        $companies = Company::all()->keyBy('name');

        $companyUsers = [
            [
                'name'         => 'Sophie Leclerc',
                'email'        => 's.leclerc@accenture.com',
                'company_name' => 'Accenture',
            ],
            [
                'name'         => 'Thomas Bernard',
                'email'        => 't.bernard@capgemini.com',
                'company_name' => 'Capgemini',
            ],
            [
                'name'         => 'Isabelle Roux',
                'email'        => 'i.roux@thalesgroup.com',
                'company_name' => 'Thales',
            ],
            [
                'name'         => 'Nicolas Garnier',
                'email'        => 'n.garnier@soprasteria.com',
                'company_name' => 'Sopra Steria',
            ],
            [
                'name'         => 'Camille Petit',
                'email'        => 'c.petit@orange.com',
                'company_name' => 'Orange',
            ],
        ];

        foreach ($companyUsers as $data) {
            $user = User::create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'password'           => Hash::make('password'),
                'role'               => 'company',
                'email_verified_at'  => now(),
            ]);
            $user->info->update([
                'company_id'         => $companies->get($data['company_name'])?->id,
                'profile_completion' => 60,
            ]);
        }
    }
}
