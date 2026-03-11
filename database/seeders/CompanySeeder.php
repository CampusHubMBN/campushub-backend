<?php
// database/seeders/CompanySeeder.php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'Accenture France',
                'siret' => '12345678901234',
                'website' => 'https://www.accenture.com/fr-fr',
                'industry' => 'Conseil & IT',
                'size' => '500+',
                'headquarters_city' => 'Paris',
                'is_partner' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ],
            [
                'name' => 'Capgemini',
                'siret' => '23456789012345',
                'website' => 'https://www.capgemini.com',
                'industry' => 'Conseil & IT',
                'size' => '500+',
                'headquarters_city' => 'Paris',
                'is_partner' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ],
            [
                'name' => 'Thales',
                'siret' => '34567890123456',
                'website' => 'https://www.thalesgroup.com',
                'industry' => 'Aérospatial & Défense',
                'size' => '500+',
                'headquarters_city' => 'Paris',
                'is_partner' => true,
                'is_verified' => true,
                'verified_at' => now(),
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}