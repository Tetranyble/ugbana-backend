<?php

namespace Database\Factories;

use App\Models\WebService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Synchronization>
 */
class SynchronizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $account = WebService::factory()->create();

        return [
            'synchronizable_type' => get_class($account),
            'synchronizable_id' => $account->id,
            'token' => $this->faker->sentence,
            'resource_id' => $this->faker->sentence,
            'expired_at' => $this->faker->dateTime,
            'last_synchronized_at' => $this->faker->dateTime,
        ];
    }
}
