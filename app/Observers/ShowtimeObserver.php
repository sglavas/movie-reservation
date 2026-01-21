<?php

namespace App\Observers;

use App\Models\Showtime;
use Carbon\Carbon;

class ShowtimeObserver
{
    /**
     * Handle the Showtime "creating" event.
     * 
     * Calculates the end time of a movie.
     * End time is calculated by adding the movie duration and a 30-minute buffer to the showtime's start_time.
     * 
     *
     * @param Showtime $showtime A showtime model
     * @return void
     */
    public function creating(Showtime $showtime): void
    {
        if(empty($showtime->end_time)){
            // Get the movie this showtime belongs to
            $movie = $showtime->movie;
    
            // Add 15 minutes before and after each movie as buffer
            $timeToAdd = $movie->duration + 30;

            $endTime = Carbon::parse($showtime->start_time)->addMinutes($timeToAdd);
    
            $showtime->end_time = $endTime;
        }

    }

}
