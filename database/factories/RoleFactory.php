<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = $this->faker->word;

        return [
            'name' => Str::slug($roles),
            'label' => Str::ucfirst($roles),
            'is_system' => $this->faker->randomElement([false, true]),
        ];
    }
}
