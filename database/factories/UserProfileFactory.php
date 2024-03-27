<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserProfile>
 */
class UserProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'education' => [
                'graduation_year' => $this->faker->date(),
                'school_name' => 'RSUST',
                'certificate' => 'BS.c',

            ],
            'job_experience' => [
                'company' => 'Harde',
                'job_title' => 'Backend Developer',
                'year_started' => now()->addYears(2)->format('Y-m-d'),
                'year_ended' => now()->addYears(2)->format('Y-m-d'),
                'description' => 'description',

            ],
            'skills' => ['Devops', 'php', 'laravel'],
        ];
    }
}
