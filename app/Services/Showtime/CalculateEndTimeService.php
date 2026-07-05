<?php

namespace App\Services\Showtime;

use App\Models\Showtime;
use Carbon\Carbon;

class CalculateEndTimeService
{
    /**
     * Calculates the end time of a showtime
     *
     * @param Showtime $showtime An in-memory Showtime model instance.
     * @return Showtime The model updated with the calculated end_time.
     */
    public function calculateEndTime(Showtime $showtime): Showtime
    {
        $calculatedEndTime = clone $showtime;

        // Get the movie this showtime belongs to
        $movie = $calculatedEndTime->movie;

        $timeToAdd = $movie?->duration + config('showtime.buffer');

        $endTime = Carbon::parse($calculatedEndTime->start_time)->addMinutes($timeToAdd);

        $calculatedEndTime->end_time = $endTime;
        
        return $calculatedEndTime;
    }
}
