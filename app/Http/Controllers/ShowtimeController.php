<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\Showtime\GroupShowtimeService;
use App\Services\Showtime\OrderDateService;
use Carbon\Carbon;
use Inertia\Inertia;

class ShowtimeController extends Controller
{
    public function __construct(
        protected GroupShowtimeService $groupingService,
        protected OrderDateService $orderingService,
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

            // Return an array with movie IDs as custom keys and an array of movie information and showtimes
            return [$movie->id => [
                'movie' => $movie,
                'showtimes' => $orderedDates
            ]];
        });

        return Inertia::render('ShowtimePage', [
            'movies' => $movies,
        ]);
    }
}
