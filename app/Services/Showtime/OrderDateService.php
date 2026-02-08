<?php

namespace App\Services\Showtime;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class OrderDateService
{
    /**
     * Create a new class instance.
     */
    public function orderDates(Collection $groupedShowtimes): Collection
    {
        // Order showtime keys by ascending (oldest first)
        $orderedDates = $groupedShowtimes->sortKeysUsing(function ($a, $b) {
            $firstPositionParsed = Carbon::parse($a);
            $secondPositionParsed = Carbon::parse($b);

            return $firstPositionParsed > $secondPositionParsed;
        });

        return $orderedDates;
    }
}
