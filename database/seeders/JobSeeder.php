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
        $admin     = User::where('role', 'admin')->first();
        $companies = Company::all()->keyBy('name');

        // Company recruiters keyed by their linked company_id
        $recruiters = User::where('role', 'company')->get()
            ->keyBy(fn ($u) => $u->info?->company_id);

        // Helper: get the recruiter user id for a given company name
        $recruiter = fn (string $companyName) =>
            $recruiters->get($companies->get($companyName)?->id)?->id ?? $admin->id;

        // ── ACCENTURE ─────────────────────────────────────────────────────────

        Job::create([
            'title'                => 'Stage Data Analyst',
            'description'          => "Rejoignez notre équipe Data & Analytics pour travailler sur des projets d'analyse de données massives pour des clients grands comptes.\n\nVous participerez à la conception de tableaux de bord (Power BI, Tableau), à l'automatisation de rapports et à la mise en place de pipelines de données.",
            'requirements'         => "- Étudiant en Master (Bac+4/5) en Informatique, Statistiques ou Data Science\n- Connaissances en Python et SQL\n- Maîtrise d'Excel et des outils de visualisation\n- Anglais courant",
            'benefits'             => "- Gratification de stage attractive (1 200 – 1 500 €/mois)\n- Télétravail partiel possible\n- Équipe internationale\n- Accès à la plateforme de formation Accenture",
            'type'                 => 'internship',
            'location_type'        => 'hybrid',
            'location_city'        => 'Paris',
            'location_country'     => 'France',
            'salary_min'           => 1200,
            'salary_max'           => 1500,
            'salary_period'        => 'monthly',
            'duration_months'      => 6,
            'start_date'           => now()->addMonths(2),
            'application_deadline' => now()->addMonth(),
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Accenture')?->id,
            'posted_by'            => $recruiter('Accenture'),
            'status'               => 'published',
            'published_at'         => now()->subDays(5),
        ]);

        Job::create([
            'title'                => 'Consultant Junior Cloud & Infrastructure',
            'description'          => "Intégrez notre practice Cloud pour accompagner nos clients dans leur migration vers le cloud (AWS, Azure, GCP). Vous interviendrez sur des missions de conseil, d'architecture et d'implémentation.",
            'requirements'         => "- Bac+5 école d'ingénieur ou université\n- Certifications cloud appréciées (AWS, Azure)\n- Capacité à travailler en mode projet\n- Mobilité géographique",
            'benefits'             => "- CDI avec rémunération attractive\n- Plan de carrière structuré\n- Certifications financées\n- Comité d'entreprise",
            'type'                 => 'cdi',
            'location_type'        => 'hybrid',
            'location_city'        => 'Paris',
            'location_country'     => 'France',
            'salary_min'           => 42000,
            'salary_max'           => 50000,
            'salary_period'        => 'yearly',
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Accenture')?->id,
            'posted_by'            => $recruiter('Accenture'),
            'status'               => 'published',
            'published_at'         => now()->subDays(8),
        ]);

        // ── CAPGEMINI ─────────────────────────────────────────────────────────

        Job::create([
            'title'                => 'Alternance Ingénieur DevOps',
            'description'          => "Intégrez notre équipe Cloud & DevOps pour participer au déploiement et à la maintenance d'infrastructures cloud. Vous travaillerez sur l'automatisation des pipelines CI/CD et la conteneurisation des applications.",
            'requirements'         => "- Étudiant en école d'ingénieur ou Master Informatique\n- Connaissances en Docker, Kubernetes\n- Scripting (Bash, Python)\n- Git et CI/CD (Jenkins, GitLab CI)",
            'benefits'             => "- Rémunération selon convention d'alternance\n- Matériel fourni (MacBook Pro)\n- Certifications AWS/Azure financées\n- Mentorat par des DevOps seniors",
            'type'                 => 'apprenticeship',
            'location_type'        => 'hybrid',
            'location_city'        => 'Lyon',
            'location_country'     => 'France',
            'duration_months'      => 24,
            'start_date'           => now()->addMonths(3),
            'application_deadline' => now()->addMonths(2),
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Capgemini')?->id,
            'posted_by'            => $recruiter('Capgemini'),
            'status'               => 'published',
            'published_at'         => now()->subDays(3),
        ]);

        Job::create([
            'title'                => 'Développeur Full Stack Java/Angular – CDD 12 mois',
            'description'          => "Rejoignez notre agence de développement pour intervenir chez un client bancaire de premier plan. Vous développerez des fonctionnalités sur une application de gestion de portefeuille en Java Spring Boot et Angular.",
            'requirements'         => "- 2 ans d'expérience minimum en Java Spring Boot\n- Bonne maîtrise d'Angular (v14+)\n- Connaissance des API REST\n- Expérience en environnement Agile/Scrum",
            'benefits'             => "- CDD renouvelable\n- Télétravail 3j/semaine\n- TR et mutuelle\n- Accès aux formations Capgemini University",
            'type'                 => 'cdd',
            'location_type'        => 'hybrid',
            'location_city'        => 'Bordeaux',
            'location_country'     => 'France',
            'salary_min'           => 38000,
            'salary_max'           => 46000,
            'salary_period'        => 'yearly',
            'duration_months'      => 12,
            'start_date'           => now()->addMonth(),
            'application_deadline' => now()->addWeeks(3),
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Capgemini')?->id,
            'posted_by'            => $recruiter('Capgemini'),
            'status'               => 'published',
            'published_at'         => now()->subDays(6),
        ]);

        // ── THALES ────────────────────────────────────────────────────────────

        Job::create([
            'title'                => 'Ingénieur Cybersécurité – CDD 12 mois',
            'description'          => "Rejoignez notre équipe cybersécurité pour renforcer la sécurité de nos systèmes critiques. Vous participerez aux audits de sécurité, aux pentests, et à la réponse aux incidents sur des projets à fort enjeu national.",
            'requirements'         => "- Diplôme Bac+5 en cybersécurité ou équivalent\n- Expérience en pentest et analyse de vulnérabilités\n- Certifications CEH, OSCP ou équivalent appréciées\n- Connaissance des normes ISO 27001",
            'benefits'             => "- Salaire compétitif\n- Projets stratégiques défense & spatial\n- Formation continue financée\n- RTT et télétravail partiel",
            'type'                 => 'cdd',
            'location_type'        => 'hybrid',
            'location_city'        => 'Toulouse',
            'location_country'     => 'France',
            'salary_min'           => 40000,
            'salary_max'           => 50000,
            'salary_period'        => 'yearly',
            'duration_months'      => 12,
            'start_date'           => now()->addMonth(),
            'application_deadline' => now()->addWeeks(3),
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Thales')?->id,
            'posted_by'            => $recruiter('Thales'),
            'status'               => 'published',
            'published_at'         => now()->subDays(2),
        ]);

        Job::create([
            'title'                => 'Stage Ingénieur IA & Traitement du Signal',
            'description'          => "Au sein de notre laboratoire de R&D, vous travaillerez sur des algorithmes de traitement du signal et d'intelligence artificielle appliqués à des systèmes radar et sonar.\n\nVous contribuerez à des travaux de recherche publiés et brevetés.",
            'requirements'         => "- Étudiant Bac+5 en traitement du signal, mathématiques appliquées ou IA\n- Maîtrise de Python (NumPy, SciPy, PyTorch)\n- Bases en traitement du signal\n- Goût pour la recherche appliquée",
            'benefits'             => "- Gratification 1 400 €/mois\n- Accès aux équipements de labo de pointe\n- Possibilité de publication\n- Opportunité d'embauche",
            'type'                 => 'internship',
            'location_type'        => 'onsite',
            'location_city'        => 'Brest',
            'location_country'     => 'France',
            'salary_min'           => 1400,
            'salary_max'           => 1400,
            'salary_period'        => 'monthly',
            'duration_months'      => 6,
            'start_date'           => now()->addMonths(2),
            'application_deadline' => now()->addMonths(5)->subWeek(),
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Thales')?->id,
            'posted_by'            => $recruiter('Thales'),
            'status'               => 'published',
            'published_at'         => now()->subDays(10),
        ]);

        // ── SOPRA STERIA ──────────────────────────────────────────────────────

        Job::create([
            'title'                => 'Alternance Consultant ERP SAP',
            'description'          => "Intégrez notre practice SAP en tant qu'alternant consultant. Vous participerez à des missions de conseil, de configuration et de déploiement de modules SAP (FI/CO, SD, MM) chez nos clients du secteur public et privé.",
            'requirements'         => "- Étudiant Bac+4/5 en informatique de gestion ou école de commerce\n- Intérêt fort pour les ERP et les processus métier\n- Rigueur et sens de l'analyse\n- Bonnes capacités de communication",
            'benefits'             => "- Rémunération alternance\n- Formation certifiante SAP financée\n- Accès réseau de 50 000 collaborateurs\n- Séminaires et team buildings",
            'type'                 => 'apprenticeship',
            'location_type'        => 'hybrid',
            'location_city'        => 'Paris',
            'location_country'     => 'France',
            'duration_months'      => 12,
            'start_date'           => now()->addMonths(2),
            'application_deadline' => now()->addMonths(6)->subWeek(),
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Sopra Steria')?->id,
            'posted_by'            => $recruiter('Sopra Steria'),
            'status'               => 'published',
            'published_at'         => now()->subDays(4),
        ]);

        Job::create([
            'title'                => 'Ingénieur Développement Python/Django – CDI',
            'description'          => "Rejoignez notre équipe Produit pour développer et maintenir notre plateforme SaaS de gestion documentaire. Stack Python/Django, PostgreSQL, Redis, hébergée sur AWS.",
            'requirements'         => "- 3 ans d'expérience minimum en Python/Django\n- Bonne maîtrise de PostgreSQL et Redis\n- Expérience AWS (EC2, RDS, S3)\n- Tests unitaires et bonnes pratiques CI/CD",
            'benefits'             => "- CDI, salaire selon profil\n- Full remote possible\n- Équipe produit de 15 personnes\n- Budget formation 2 000 €/an",
            'type'                 => 'cdi',
            'location_type'        => 'remote',
            'location_city'        => 'Remote',
            'location_country'     => 'France',
            'salary_min'           => 45000,
            'salary_max'           => 58000,
            'salary_period'        => 'yearly',
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Sopra Steria')?->id,
            'posted_by'            => $recruiter('Sopra Steria'),
            'status'               => 'published',
            'published_at'         => now()->subDays(7),
        ]);

        // ── ORANGE ────────────────────────────────────────────────────────────

        Job::create([
            'title'                => 'Stage Développeur Mobile React Native',
            'description'          => "Au sein de l'équipe Orange Services, vous contribuerez au développement de l'application mobile Orange Bank (iOS & Android) en React Native. Vous travaillerez sur des features utilisateurs, les tests et la performance.",
            'requirements'         => "- Étudiant Bac+4/5 en développement mobile ou web\n- Bonne connaissance de React Native\n- Notions de TypeScript\n- Curiosité et esprit d'équipe",
            'benefits'             => "- Gratification 1 100 – 1 300 €/mois\n- Tickets restaurant\n- Smartphone de test fourni\n- Possible embauche alternance",
            'type'                 => 'internship',
            'location_type'        => 'hybrid',
            'location_city'        => 'Paris',
            'location_country'     => 'France',
            'salary_min'           => 1100,
            'salary_max'           => 1300,
            'salary_period'        => 'monthly',
            'duration_months'      => 6,
            'start_date'           => now()->addMonths(2),
            'application_deadline' => now()->addMonth(),
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Orange')?->id,
            'posted_by'            => $recruiter('Orange'),
            'status'               => 'published',
            'published_at'         => now()->subDays(1),
        ]);

        Job::create([
            'title'                => 'Ingénieur Réseaux & Télécoms – CDI',
            'description'          => "Rejoignez notre direction technique pour concevoir, déployer et superviser les infrastructures réseau cœur et accès (4G/5G, fibre, MPLS). Vous travaillerez sur des projets d'envergure nationale.",
            'requirements'         => "- Diplôme d'ingénieur en télécoms ou réseaux\n- Expérience 2 ans minimum en réseaux opérateurs\n- Maîtrise Cisco/Juniper, BGP, MPLS\n- Habilitation sécurité appréciée",
            'benefits'             => "- CDI, rémunération attractive\n- Véhicule de fonction possible\n- Plan d'épargne entreprise\n- 37 jours de congés",
            'type'                 => 'cdi',
            'location_type'        => 'onsite',
            'location_city'        => 'Rennes',
            'location_country'     => 'France',
            'salary_min'           => 44000,
            'salary_max'           => 55000,
            'salary_period'        => 'yearly',
            'source_type'          => 'internal',
            'company_id'           => $companies->get('Orange')?->id,
            'posted_by'            => $recruiter('Orange'),
            'status'               => 'published',
            'published_at'         => now()->subDays(9),
        ]);

        // ── EXTERNAL ──────────────────────────────────────────────────────────

        Job::create([
            'title'            => 'Software Engineer Intern – Google Paris',
            'description'      => "Join Google's engineering team to work on cutting-edge projects impacting billions of users. You'll contribute to core infrastructure, machine learning systems, or product features depending on team placement.",
            'requirements'     => "- Currently pursuing a Bachelor's or Master's in Computer Science\n- Strong coding skills (C++, Java, or Python)\n- Solid algorithms & data structures fundamentals\n- Fluent English",
            'type'             => 'internship',
            'location_type'    => 'onsite',
            'location_city'    => 'Paris',
            'location_country' => 'France',
            'duration_months'  => 6,
            'start_date'       => now()->addMonths(4),
            'application_url'  => 'https://careers.google.com',
            'external_url'     => 'https://careers.google.com',
            'source_type'      => 'external',
            'company_name'     => 'Google',
            'posted_by'        => $admin->id,
            'status'           => 'published',
            'published_at'     => now()->subWeek(),
        ]);

        Job::create([
            'title'            => 'Product Manager Intern – Microsoft Azure',
            'description'      => "Work on Azure cloud products, collaborate closely with engineering and design teams, and help shape the future of enterprise cloud computing.",
            'type'             => 'internship',
            'location_type'    => 'remote',
            'location_city'    => 'Remote',
            'location_country' => 'France',
            'duration_months'  => 6,
            'application_url'  => 'https://careers.microsoft.com',
            'external_url'     => 'https://careers.microsoft.com',
            'source_type'      => 'external',
            'company_name'     => 'Microsoft',
            'posted_by'        => $admin->id,
            'status'           => 'published',
            'published_at'     => now()->subDays(10),
        ]);

        Job::create([
            'title'            => 'Ingénieur Machine Learning – Mistral AI',
            'description'      => "Rejoignez l'équipe de recherche de Mistral AI pour travailler sur les prochaines générations de grands modèles de langage. Vous contribuerez à l'entraînement, à l'évaluation et à l'optimisation des modèles.",
            'requirements'     => "- Master ou Doctorat en ML/DL\n- Expérience en PyTorch\n- Contributions open source appréciées\n- Publications dans des conférences ML appréciées",
            'type'             => 'cdi',
            'location_type'    => 'onsite',
            'location_city'    => 'Paris',
            'location_country' => 'France',
            'salary_min'       => 60000,
            'salary_max'       => 100000,
            'salary_period'    => 'yearly',
            'application_url'  => 'https://mistral.ai/fr/careers',
            'external_url'     => 'https://mistral.ai/fr/careers',
            'source_type'      => 'external',
            'company_name'     => 'Mistral AI',
            'posted_by'        => $admin->id,
            'status'           => 'published',
            'published_at'     => now()->subDays(3),
        ]);
    }
}
