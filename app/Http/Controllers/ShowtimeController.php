<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Http\Resources\ScreenResource;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use App\Rules\ShowtimeOverlapRule;
use App\Services\Showtime\CalculateEndTimeService;
use App\Services\Showtime\ShowtimePipelineService;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ShowtimeController extends Controller
{
    public function __construct(
        protected CalculateEndTimeService $calculatingService,
        protected ShowtimeOverlapRule $overlapRule,
        protected ShowtimePipelineService $pipelineService,
    )
    {
        //
    }

    public function index()
    {
        // Get all screens from the database and sort them according to screen id
        $screens = Screen::all()->mapWithKeys(function ($screen) {
            return[$screen->id => new ScreenResource($screen)];
        });

        // Fetch the movies together with the corresponding showtimes and map over the array
        $movies = Movie::with('showtimes')->get()->mapWithKeys(function ($movie) {

            // Funnel movies and showtimes through the pipeline to get
            $bookableDates = $this->pipelineService->usePipeline($movie);

            // Return an array with movie IDs as custom keys and an array of movie information and showtimes
            return [$movie->id => [
                'movie' => new MovieResource($movie),                           // Filter data using MovieResource
                'showtimes' => $bookableDates
            ]];
        });

        return Inertia::render('Showtimes/Index', [
            'movies' => $movies,
        ]);
    }
}
