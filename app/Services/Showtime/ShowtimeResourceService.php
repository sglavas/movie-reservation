<?php

namespace App\Services\Showtime;

use App\Http\Resources\Public\ShowtimeScheduleResource;
use Illuminate\Support\Collection;

class ShowtimeResourceService
{
    /**
     * Create a new class instance.
     */
    public function useResource (Collection $orderedDates): Collection
    {
        $filteredDates = $orderedDates->map(function (Collection $showtimesForDay) {
            return ShowtimeScheduleResource::collection($showtimesForDay);
        });

        return $filteredDates;
    }
}
