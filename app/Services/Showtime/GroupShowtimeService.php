<?php

namespace App\Services\Showtime;

use App\Models\Movie;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class GroupShowtimeService
{
    /**
     * Create a new class instance.
     */
    public function groupShowtimes (Movie $movie): Collection
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
}
