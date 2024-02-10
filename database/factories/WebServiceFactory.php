<?php

namespace Database\Factories;

use App\Enums\StorageProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WebService>
 */
class WebServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider' => StorageProvider::GOOGLE->value,
            'name' => $this->faker->sentence,
            'user_id' => User::factory(),
            'client_id' => $this->faker->sentence,
            'token' => ['name' => $this->faker->sentence],
            'scopes' => ['calendar', 'meet'],
        ];
    }
}
