<?php

namespace App\Services\Showtime;

use Carbon\Carbon;

class OrderDateService
{
    /**
     * Create a new class instance.
     */
    public function orderDates($groupedShowtimes)
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
