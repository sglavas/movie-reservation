<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Screen;
use App\Services\Seeder\ExtractSeedService;
use Illuminate\Support\Facades\DB;

class ScreenSeeder extends Seeder
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
        $fileName = database_path('seeds/csvs/joker-screens.csv');

        $data = $this->extractionService->extractData($fileName);

        $this->command->info('Creating sample screens...');

        DB::transaction(function () use($data) {
            Screen::insert($data);
        });

    }
}
