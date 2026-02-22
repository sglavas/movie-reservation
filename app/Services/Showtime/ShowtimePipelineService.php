<?php

namespace App\Services\Showtime;

use App\Models\Movie;
use Illuminate\Support\Collection;

class ShowtimePipelineService
{
    /**
     * Create a new ShowtimePipelineService instance
     *
     * @param GroupShowtimeService $groupingService Groups showtimes by date
     * @param OrderDateService $orderingService Orders showtimes by ascending
     * @param ShowtimeResourceService $filteringService Filters data using ShowtimeResource
     * @param SetBookableService $bookableService Sets the bookable property
     */
    public function __construct(
        protected GroupShowtimeService $groupingService,
        protected OrderDateService $orderingService,
        protected ShowtimeResourceService $filteringService,
        protected SetBookableService $bookableService,

    )
    {
        //
    }

    /**
     * Run the movie showtimes through the preparation pipeline
     *
     * @param Movie $movie The movie model with loaded showtimes.
     * @return Collection A collection of grouped, ordered, and bookable showtimes.
     */
    public function usePipeline(Movie $movie): Collection
    {
            // Group showtimes by date and time string
            $groupedShowtimes = $this->groupingService->groupShowtimes($movie);

            // Order showtime keys by ascending (oldest first)
            $orderedDates = $this->orderingService->orderDates($groupedShowtimes);

            // Filter database data using ShowtimeResource
            $filteredDates = $this->filteringService->useResource($orderedDates);

            // If start time is after now, set is_bookable showtime property to false
            $bookableDates = $this->bookableService->setBookable($filteredDates);

            return $bookableDates;

    }
}
