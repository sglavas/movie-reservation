<?php

namespace App\Services\Showtime;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class SetBookableService
{
    /**
     * Create a new class instance.
     */
    public function setBookable(Collection $filteredDates): Collection
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
}
