<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Seat;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileName = database_path('seeds/csvs/seats.csv');

        $data = csvToArray($fileName);

        // If data doesn't exist
        if(!$data){
            $this->command->error("Data could not be retrieved");
            return;
        }

        $this->command->info("Creating sample seats...");

        Seat::insert($data);
    }

}
