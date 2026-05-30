<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seat;
use App\Services\Seeder\ExtractSeedService;
use Illuminate\Support\Facades\DB;

class SeatSeeder extends Seeder
{
    public function __construct(
        protected ExtractSeedService $extractionService,
    )
    {
        //
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fileName = database_path('seeds/csvs/seats.csv');

        $data = $this->extractionService->extractData($fileName);

        $this->command->info("Creating sample seats...");

        DB::transaction(function () use($data) {
            Seat::insert($data);
        });
    }

}
