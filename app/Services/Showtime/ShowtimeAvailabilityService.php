<?php

namespace App\Services\Showtime;

use App\Models\Showtime;
use Illuminate\Support\Facades\DB;

class ShowtimeAvailabilityService
{
    public function __construct(
        protected CalculateEndTimeService $calculatingService,
    )
    {
        //
    }
    /**
     * Check if a new showtime overlaps with existing ones.
     *
     * Uses the "Standard Overlap" formula: (StartA < EndB) AND (EndA > StartB)
     * @param array $data {movie: int, screen: int, date: string, time: string, subtitles: bool, is_3d: bool, dubbed: bool}
     * @return boolean True if there is overlap, false if not.
     */
    public function validateOverlap(array $data): bool
    {
        [
            'movie' => $movie,
            'screen' => $screen,
            'date' => $date,
            'time' => $time,
            'subtitles' => $subtitles,
            'is_3d' => $is_3d,
            'dubbed' => $dubbed,
        ] = $data;

        $startDateTime = date('Y-m-d H:i:s', strtotime("$date $time"));

        // Create an in-memory Showtime model with the form input
        $showtimeModel = new Showtime([
            'movie_id' => $movie,
            'screen_id' => $screen,
            'start_time' => $startDateTime,
            'end_time' => null,
            'subtitles' => $subtitles,
            'is_3d' => $is_3d,
            'dubbed' => $dubbed,
        ]);

        // Calculate end time
        $calculatedShowtime = $this->calculatingService->calculateEndTime($showtimeModel);

        // Determine if showtime overlap exists
        $overlapExists = DB::table('showtimes')
                        ->where('screen_id', $screen)
                        ->where([
                            ['start_time', '<', $calculatedShowtime->end_time->format('Y-m-d H:i:s')],
                            ['end_time', '>', $startDateTime],
                        ])
                        ->exists();
                        
        return $overlapExists;
    }
}
