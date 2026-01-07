<?php

namespace Database\Seeders;

use App\Models\Theater;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TheaterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileName = database_path('seeds/csvs/theaters.csv');

        $data = csvToArray($fileName);

        // If data doesn't exist
        if(!$data){
            $this->command->error("Data could not be retrieved");
            return;
        }

        $this->command->info((('Creating sample theaters...')));

        Theater::insert($data);
    }
}
