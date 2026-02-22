<?php

namespace Database\Factories;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Showtime>
 */
class ShowtimeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'movie_id' => rand(1, 10),
            'screen_id' => rand(1, 5),
            'start_time' => Carbon::now()->addDays(rand(0, 2))->startOfDay()->addHours(rand(8, 22)),
            'end_time' => null,
            'subtitles' => rand(0, 1),
            'is_3d' => rand(0, 1),
            'dubbed' => rand(0, 1),
        ];
    }

}
