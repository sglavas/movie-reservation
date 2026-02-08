<?php

namespace App\Services\Showtime;

use App\Http\Resources\ShowtimeResource;
use Illuminate\Support\Collection;

class ShowtimeResourceService
{
    /**
     * Create a new class instance.
     */
    public function useResource (Collection $orderedDates): Collection
    {
        $filteredDates = $orderedDates->map(function (Collection $showtimesForDay) {
            return ShowtimeResource::collection($showtimesForDay);
        });

        return $filteredDates;
    }
}
