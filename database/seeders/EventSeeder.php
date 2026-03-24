<?php
// database/seeders/EventSeeder.php

namespace Database\Seeders;

use App\Models\CampusEvent;
use App\Models\User;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $bde = User::where('role', 'bde_member')->first();
        $admin = User::where('role', 'admin')->first();
        $pedagogical = User::where('role', 'pedagogical')->first();

        // Fallback to admin if roles don't exist yet
        $organizer = $bde ?? $admin;
        $pedagOrganizer = $pedagogical ?? $admin;

        $events = [
            [
                'title'       => 'Forum des entreprises CampusHub 2026',
                'description' => "Le grand rendez-vous annuel entre étudiants, alumni et entreprises partenaires. Venez rencontrer plus de 30 recruteurs, assister à des conférences sur l'insertion professionnelle et participer à des ateliers CV/entretien.\n\nAu programme :\n- Speed networking avec les RH\n- Conférences \"Mon parcours alumni\"\n- Ateliers LinkedIn et CV\n- Remise des prix des meilleurs projets étudiants",
                'location'    => 'Grand Amphithéâtre, Campus Principal',
                'start_date'  => now()->addDays(14)->setHour(9)->setMinute(0),
                'end_date'    => now()->addDays(14)->setHour(18)->setMinute(0),
                'capacity'    => 300,
                'event_type'  => 'networking',
                'target_roles' => ['student', 'alumni'],
                'organizer_id' => $organizer->id,
                'published_at' => now()->subDay(),
            ],
            [
                'title'       => 'Hackathon IA & Innovation',
                'description' => "48h pour créer un projet innovant utilisant l'intelligence artificielle. En équipes de 3 à 5 personnes, relevez les défis proposés par nos entreprises partenaires. Des mentors experts seront disponibles tout au long de l'événement.\n\nPrix à gagner :\n- 1er prix : 2 000€ + stage garanti\n- 2e prix : 1 000€\n- 3e prix : Matériel tech",
                'location'    => 'Salle Innovation, Bâtiment B',
                'start_date'  => now()->addDays(21)->setHour(14)->setMinute(0),
                'end_date'    => now()->addDays(23)->setHour(14)->setMinute(0),
                'capacity'    => 80,
                'event_type'  => 'workshop',
                'target_roles' => ['student', 'alumni'],
                'organizer_id' => $organizer->id,
                'published_at' => now()->subDays(2),
            ],
            [
                'title'       => 'Conférence : Cybersécurité et enjeux 2026',
                'description' => "Intervenants de haut niveau issus de l'ANSSI, de grandes ESN et de startups spécialisées vous présenteront les dernières menaces cyber et les métiers porteurs dans ce domaine en pleine expansion.\n\nIdéal pour les étudiants en informatique, réseaux et systèmes.",
                'location'    => 'Amphithéâtre Curie, Campus Tech',
                'start_date'  => now()->addDays(7)->setHour(14)->setMinute(0),
                'end_date'    => now()->addDays(7)->setHour(17)->setMinute(30),
                'capacity'    => 150,
                'event_type'  => 'conference',
                'target_roles' => null, // open to all
                'organizer_id' => $pedagOrganizer->id,
                'published_at' => now()->subDays(3),
            ],
            [
                'title'       => 'Atelier Soft Skills : Prise de parole en public',
                'description' => "Maîtrisez l'art de captiver votre audience ! Cet atelier pratique vous donnera les clés pour présenter avec confiance, gérer le stress et structurer un discours percutant.\n\nFormat : petits groupes (max 20 personnes) avec mises en situation réelles.",
                'location'    => 'Salle de conférence 204, Bâtiment A',
                'start_date'  => now()->addDays(5)->setHour(10)->setMinute(0),
                'end_date'    => now()->addDays(5)->setHour(13)->setMinute(0),
                'capacity'    => 20,
                'event_type'  => 'workshop',
                'target_roles' => ['student', 'alumni'],
                'organizer_id' => $pedagOrganizer->id,
                'published_at' => now()->subDays(1),
            ],
            [
                'title'       => 'Tournoi sportif inter-promo',
                'description' => "La grande compétition sportive annuelle ! Football, basketball, badminton et volleyball au programme. Inscrivez votre équipe et représentez votre promotion.\n\nUne journée de cohésion, de sport et de bonne humeur !",
                'location'    => 'Complexe sportif universitaire',
                'start_date'  => now()->addDays(30)->setHour(9)->setMinute(0),
                'end_date'    => now()->addDays(30)->setHour(18)->setMinute(0),
                'capacity'    => 200,
                'event_type'  => 'sports',
                'target_roles' => ['student'],
                'organizer_id' => $organizer->id,
                'published_at' => now()->subDays(1),
            ],
            [
                'title'       => 'Soirée Alumni : Networking & Échanges',
                'description' => "Retrouvez les anciens étudiants de CampusHub dans un cadre convivial. Partagez vos expériences professionnelles, créez des liens et découvrez les opportunités de mentorat pour les étudiants actuels.\n\nBuffet et boissons offerts. Inscription obligatoire.",
                'location'    => 'Rooftop du Campus, 5e étage',
                'start_date'  => now()->addDays(45)->setHour(19)->setMinute(0),
                'end_date'    => now()->addDays(45)->setHour(23)->setMinute(0),
                'capacity'    => 100,
                'event_type'  => 'networking',
                'target_roles' => ['student', 'alumni'],
                'organizer_id' => $organizer->id,
                'published_at' => now()->subDays(1),
            ],
        ];

        foreach ($events as $data) {
            CampusEvent::create($data);
        }

        $this->command->info('EventSeeder: ' . count($events) . ' events created.');
    }
}
