<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Seeder\ExtractSeedService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
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
        $fileName = database_path('seeds/csvs/users.csv');

        $data = $this->extractionService->extractData($fileName);

        $this->command->info("Creating sample users...");

        DB::transaction(function () use($data) {
            foreach($data as $record){
                User::create($record);
            }
        });

    }
}
