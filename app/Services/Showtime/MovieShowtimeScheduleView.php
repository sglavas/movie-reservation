<?php

namespace App\Services\Showtime;

use App\Models\Movie;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class MovieShowtimeScheduleView
{
    /**
     * Create a new ShowtimePipelineService instance
     *
     * @param ShowtimeResourceService $filteringService Filters data using ShowtimeResource
     */
    public function __construct(
        protected ShowtimeResourceService $filteringService,

    )
    {
        //
    }

    private function groupShowtimes (Movie $movie): Collection
    {
        // Group the showtimes according to date
        $groupedShowtimes = $movie->showtimes->groupBy(function ($showtime) {
            $parsedTime = Carbon::parse($showtime['start_time']);

            // Group today's showtimes by "today, Month Day"
            if($parsedTime->isToday()){
                // return 'today,' . " " . $parsedTime->format('M d');
                return 'today';
            }

            // Group tomorrow's showtimes by "tomorrow, Month Day"
            if($parsedTime->isTomorrow()){
                // return 'tomorrow, ' . " " . $parsedTime->format('M d');
                return 'tomorrow';
            }

            // Create a period between the first day of the month and the last day of the month
            $period = CarbonPeriod::between(Carbon::parse($parsedTime)->startOfMonth(), Carbon::parse($parsedTime)->endOfMonth())
                ->filter(function ($date) {
                    return $date;
                });
            
            // Loop over the generated period to find the relevant day
            foreach($period as $day){
                if(Carbon::parse($day)->isSameDay($parsedTime)){
                    // Group by date (Day of the week, Month Day)
                    return $parsedTime->format('D') . ", " . $parsedTime->format('M d');
                }
            }

        });

        return $groupedShowtimes;

    }


    private function orderDates(Collection $groupedShowtimes): Collection
    {
        // Order showtime keys by ascending (oldest first)
        $orderedDates = $groupedShowtimes->sortKeysUsing(function ($a, $b) {
            $firstPositionParsed = Carbon::parse($a);
            $secondPositionParsed = Carbon::parse($b);

            return $firstPositionParsed > $secondPositionParsed;
        });

        //Sort showtime grid by screen number
        $sortedByScreen = $orderedDates->map(function($sortedShwotime) {
            return $sortedShwotime->sortBy('screen_id')->values();
        });

        // Sort showtime grid by date and time
        $sortedByTime = $sortedByScreen->map(function($orderedDate) {
            return $orderedDate->sortBy('start_time')->values();
        });


        return $sortedByTime;
    }


    private function setBookable(Collection $filteredDates): Collection
    {
        $bookableDates =  $filteredDates->map(function($filteredDate) {
            $bookableDate =  $filteredDate->map(function($showtime) {

                // If the start time of a showtime is after the current date and time, set is_bookable to false
                $showtimeCollection = collect($showtime)->put('is_bookable', !Carbon::now()->isAfter($showtime->start_time));
                return $showtimeCollection;

            });
            return $bookableDate;
        });

        return $bookableDates;
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
            $groupedShowtimes = $this->groupShowtimes($movie);

            // Order showtime keys by ascending (oldest first)
            $orderedDates = $this->orderDates($groupedShowtimes);

            // Filter database data using ShowtimeResource
            $filteredDates = $this->filteringService->useResource($orderedDates);

            // If start time is after now, set is_bookable showtime property to false
            $bookableDates = $this->setBookable($filteredDates);

            return $bookableDates;

    }
}
