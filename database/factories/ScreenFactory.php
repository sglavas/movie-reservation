<?php

namespace Database\Factories;

use App\Models\Theater;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Screen>
 */
class ScreenFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->numberBetween(1, 10),
            'theater_id' => Theater::factory(),
            'regular_seats' => 50,
            'couples_seats' => 50,
            'vip_seats' => 50,
            'disability_seats' => 50,
            'royal_seats' => 50,
            'total_seats' => 50,
        ];
    }
}
