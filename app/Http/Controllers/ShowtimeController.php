<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Http\Resources\ScreenResource;
use App\Models\Movie;
use App\Services\Showtime\GroupShowtimeService;
use App\Services\Showtime\OrderDateService;
use App\Services\Showtime\SetBookableService;
use App\Services\Showtime\ShowtimeResourceService;
use Carbon\Carbon;
use Inertia\Inertia;

class ShowtimeController extends Controller
{
    public function __construct(
        protected GroupShowtimeService $groupingService,
        protected OrderDateService $orderingService,
        protected ShowtimeResourceService $filteringService,
        protected SetBookableService $bookableService
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

            // Group showtimes by date and time string
            $groupedShowtimes = $this->groupingService->groupShowtimes($movie);

            // Order showtime keys by ascending (oldest first)
            $orderedDates = $this->orderingService->orderDates($groupedShowtimes);

            // Filter database data using ShowtimeResource
            $filteredDates = $this->filteringService->useResource($orderedDates);

            // If start time is after now, set is_bookable showtime property to false
            $bookableDates = $this->bookableService->setBookable($filteredDates);

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
