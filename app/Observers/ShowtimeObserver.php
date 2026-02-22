<?php

namespace App\Observers;

use App\Models\Showtime;
use App\Services\Showtime\CalculateEndTimeService;

class ShowtimeObserver
{
    public function __construct(
        protected CalculateEndTimeService $calculatingService
    )
    {
        //
    }
    /**
     * Handle the Showtime "creating" event.
     * 
     * Calculates the end time of a movie.
     * End time is calculated by adding the movie duration and a 30-minute buffer to the showtime's start_time.
     * Mutate the original object by adding the calculated end time
     *
     * @param Showtime $showtime A showtime model
     * @return void
     */
    public function creating(Showtime $showtime): void
    {
        // Get the Showtime with the calculated end time
        $calculatedEndTime = $this->calculatingService->calculateEndTime($showtime);

        // Mutate the original Showtime model
        $showtime->end_time = $calculatedEndTime->end_time;

    }

}
