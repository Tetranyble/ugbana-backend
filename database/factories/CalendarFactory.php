<?php

namespace Database\Factories;

use App\Models\WebService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calendar>
 */
class CalendarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'google_id' => $this->faker->sentence,
            'name' => $this->faker->sentence,
            'color' => $this->faker->sentence,
            'timezone' => $this->faker->sentence,
            'is_primary' => $this->faker->sentence,
            'web_service_id' => WebService::factory(),
        ];
    }
}
