<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Models\Movie;
use App\Services\Showtime\GroupShowtimeService;
use App\Services\Showtime\OrderDateService;
use App\Services\Showtime\ShowtimeResourceService;
use Carbon\Carbon;
use Inertia\Inertia;

class ShowtimeController extends Controller
{
    public function __construct(
        protected GroupShowtimeService $groupingService,
        protected OrderDateService $orderingService,
        protected ShowtimeResourceService $filteringService,
    )
    {
        //
    }

    public function index()
    {
        // Fetch the movies together with the corresponding showtimes and map over the array
        $movies = Movie::with('showtimes')->get()->mapWithKeys(function ($movie) {

            $groupedShowtimes = $this->groupingService->groupShowtimes($movie);

            // Order showtime keys by ascending (oldest first)
            $orderedDates = $this->orderingService->orderDates($groupedShowtimes);

            // Filter database data using ShowtimeResource
            $filteredDates = $this->filteringService->useResource($orderedDates);

            // Return an array with movie IDs as custom keys and an array of movie information and showtimes
            return [$movie->id => [
                'movie' => new MovieResource($movie),                           // Filter data using MovieResource
                'showtimes' => $filteredDates
            ]];
        });

        return Inertia::render('ShowtimePage', [
            'movies' => $movies,
        ]);
    }
}
