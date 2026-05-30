<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(TheaterSeeder::class);
        $this->call(ScreenSeeder::class);
        $this->call(SeatSeeder::class);
        $this->call(MovieSeeder::class);
        $this->call(ShowtimeSeeder::class);
        $this->call(UserSeeder::class);

        User::factory()->create([
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'password' => 'password',
        ]);
    }
}
