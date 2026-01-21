<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Movie;
use App\Services\Seeder\ExtractSeedService;
use Illuminate\Support\Facades\DB;

class MovieSeeder extends Seeder
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
        $fileName = database_path('seeds/csvs/movies.csv');

        $data = $this->extractionService->extractData($fileName);

        $this->command->info('Creating sample movies...');

        DB::transaction(function () use($data) {
            Movie::insert($data);
        });

    }
}
