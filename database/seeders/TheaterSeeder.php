<?php

namespace Database\Seeders;

use App\Models\Theater;
use App\Services\Seeder\ExtractSeedService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TheaterSeeder extends Seeder
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
        $fileName = database_path('seeds/csvs/theaters.csv');

        $data = $this->extractionService->extractData($fileName);

        $this->command->info((('Creating sample theaters...')));

        DB::transaction(function () use($data) {
            Theater::insert($data);
        });
    }
}
