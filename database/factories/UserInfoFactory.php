<?php
// database/factories/UserInfoFactory.php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserInfoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'bio' => fake()->paragraph(),
            'phone' => fake()->phoneNumber(),
            'linkedin_url' => 'https://linkedin.com/in/' . fake()->userName(),
            'github_url' => 'https://github.com/' . fake()->userName(),
            'skills' => ['PHP', 'JavaScript', 'Laravel', 'React'],
            'program' => 'Master Informatique',
            'year' => fake()->numberBetween(1, 5),
            'campus' => fake()->randomElement(['Paris', 'Lyon', 'Marseille']),
            'reputation_points' => fake()->numberBetween(0, 500),
            'level' => 'beginner',
            'profile_completion' => 60,
        ];
    }
}