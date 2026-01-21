<?php

namespace Database\Seeders;

use App\Models\Showtime;
use App\Services\Seeder\ExtractSeedService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShowtimeSeeder extends Seeder
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
        $fileName = database_path('seeds/csvs/showtime.csv');

        $data = $this->extractionService->extractData($fileName);

        $this->command->info("Creating sample showtimes...");

        DB::transaction(function () use($data) {
            foreach($data as $record){
                Showtime::create($record);
            }
        });

    }
}
