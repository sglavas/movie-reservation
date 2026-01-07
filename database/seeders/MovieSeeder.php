<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Movie;

class MovieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileName = database_path('seeds/csvs/movies.csv');

        $data = csvToArray($fileName);

        // If data doesn't exist
        if(!$data){
            $this->command->error("Data could not be retireved.");
            return;
        }

        $this->command->info('Creating sample movies...');

        Movie::insert($data);
    }
}
