<?php
// database/seeders/JobSeeder.php

namespace Database\Seeders;

use App\Models\Job;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $companies = Company::all();

        // Job 1 - Accenture - Stage Data Analyst (Internal)
        Job::create([
            'title' => 'Stage Data Analyst',
            'description' => 'Rejoignez notre équipe data pour travailler sur des projets d\'analyse de données massives. Vous participerez à la conception de tableaux de bord et à l\'automatisation de rapports.',
            'requirements' => "- Étudiant en Master (Bac+4/5)\n- Connaissances en Python et SQL\n- Maîtrise d'Excel\n- Anglais courant",
            'benefits' => "- Gratification de stage attractive\n- Télétravail partiel possible\n- Équipe internationale\n- Accès à la formation Accenture",
            'type' => 'internship',
            'location_type' => 'hybrid',
            'location_city' => 'Paris',
            'location_country' => 'France',
            'salary_min' => 1200,
            'salary_max' => 1500,
            'salary_period' => 'monthly',
            'duration_months' => 6,
            'start_date' => now()->addMonths(2),
            'application_deadline' => now()->addMonth(),
            'source_type' => 'internal',
            'company_id' => $companies->where('name', 'Accenture')->first()?->id,
            'posted_by' => $admin->id,
            'status' => 'published',
            'published_at' => now()->subDays(5),
        ]);

        // Job 2 - Capgemini - Alternance DevOps (Internal)
        Job::create([
            'title' => 'Alternance Ingénieur DevOps',
            'description' => 'Intégrez notre équipe Cloud & DevOps pour participer au déploiement et à la maintenance d\'infrastructures cloud (AWS, Azure). Vous travaillerez sur l\'automatisation des pipelines CI/CD.',
            'requirements' => "- Étudiant en école d'ingénieur ou Master Informatique\n- Connaissances en Docker, Kubernetes\n- Scripting (Bash, Python)\n- Git et CI/CD (Jenkins, GitLab CI)",
            'benefits' => "- Rémunération selon convention alternance\n- Matériel fourni (MacBook Pro)\n- Certifications AWS/Azure financées\n- Mentorat par des DevOps seniors",
            'type' => 'apprenticeship',
            'location_type' => 'hybrid',
            'location_city' => 'Lyon',
            'location_country' => 'France',
            'salary_min' => null,
            'salary_max' => null,
            'duration_months' => 24,
            'start_date' => now()->addMonths(3),
            'application_deadline' => now()->addMonths(2),
            'source_type' => 'internal',
            'company_id' => $companies->where('name', 'Capgemini')->first()?->id,
            'posted_by' => $admin->id,
            'status' => 'published',
            'published_at' => now()->subDays(3),
        ]);

        // Job 3 - External - Google - Software Engineer Intern
        Job::create([
            'title' => 'Software Engineer Intern - Google Paris',
            'description' => 'Join Google\'s engineering team to work on cutting-edge projects. You\'ll contribute to products used by billions of users worldwide.',
            'requirements' => "- Currently pursuing a Bachelor's or Master's degree in Computer Science\n- Strong coding skills (C++, Java, Python)\n- Problem-solving abilities\n- Fluent in English",
            'type' => 'internship',
            'location_type' => 'onsite',
            'location_city' => 'Paris',
            'location_country' => 'France',
            'duration_months' => 6,
            'start_date' => now()->addMonths(4),
            'application_url' => 'https://careers.google.com/jobs/results/123456789/',
            'external_url' => 'https://careers.google.com/jobs/results/123456789/',
            'source_type' => 'external',
            'company_name' => 'Google',
            'posted_by' => $admin->id,
            'status' => 'published',
            'published_at' => now()->subWeek(),
        ]);

        // Job 4 - Thales - CDD Cybersecurity Engineer (Internal)
        Job::create([
            'title' => 'Ingénieur Cybersécurité - CDD 12 mois',
            'description' => 'Rejoignez notre équipe cybersécurité pour renforcer la sécurité de nos systèmes critiques. Vous participerez aux audits de sécurité, pentests, et à la réponse aux incidents.',
            'requirements' => "- Diplôme Bac+5 en cybersécurité ou équivalent\n- Expérience en pentest et analyse de vulnérabilités\n- Certifications CEH, OSCP ou équivalent (apprécié)\n- Connaissances des normes ISO 27001",
            'benefits' => "- Salaire compétitif\n- Projets stratégiques défense\n- Formation continue\n- RTT et télétravail",
            'type' => 'cdd',
            'location_type' => 'hybrid',
            'location_city' => 'Toulouse',
            'location_country' => 'France',
            'salary_min' => 40000,
            'salary_max' => 50000,
            'salary_period' => 'yearly',
            'duration_months' => 12,
            'start_date' => now()->addMonths(1),
            'application_deadline' => now()->addWeeks(3),
            'source_type' => 'internal',
            'company_id' => $companies->where('name', 'Thales')->first()?->id,
            'posted_by' => $admin->id,
            'status' => 'published',
            'published_at' => now()->subDays(2),
        ]);

        // Job 5 - External - Microsoft - Product Manager Intern
        Job::create([
            'title' => 'Product Manager Intern - Microsoft Azure',
            'description' => 'Work on Azure cloud products, collaborate with engineering teams, and help shape the future of cloud computing.',
            'type' => 'internship',
            'location_type' => 'remote',
            'location_city' => 'Remote',
            'location_country' => 'France',
            'duration_months' => 6,
            'application_url' => 'https://careers.microsoft.com/v2/global/en/job/1234567',
            'external_url' => 'https://careers.microsoft.com/v2/global/en/job/1234567',
            'source_type' => 'external',
            'company_name' => 'Microsoft',
            'posted_by' => $admin->id,
            'status' => 'published',
            'published_at' => now()->subDays(10),
        ]);
    }
}