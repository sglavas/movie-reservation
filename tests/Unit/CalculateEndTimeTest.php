<?php

namespace Tests\Unit;

use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use App\Services\Showtime\CalculateEndTimeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;

class CalculateEndTimeTest extends TestCase
{
    use RefreshDatabase;


    private Movie $movie;
    private Screen $screen;


    protected function setUp(): void
    {
        parent::setUp();

        $this->movie = Movie::factory()->create();

        $this->screen = Screen::factory()->create();
        
    }

    public function test_calculates_end_time(): void
    {
        /* ARRANGE */
        $showtime = new Showtime([
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen->id,
            'start_time' => '13:00 2026-02-17',
            'end_time' => null,
            'subtitles' => 1,
            'is_3d' => 0,
            'dubbed' => 0,
        ]);

        $showtimeStart = Carbon::parse('2026-02-17 13:00');

        $timeToAdd = $this->movie->duration + config('showtime.buffer');
        $showtimeEnd = $showtimeStart->copy()->addMinutes($timeToAdd);

        /* ACT */
        // Calculate the end time using the CalculateEndTimeService class
        $calculatedEndTime = (new CalculateEndTimeService)->calculateEndTime($showtime);

        /* ASSERT */
        assertEquals($showtimeEnd->toDateTimeString(), $calculatedEndTime->end_time->toDateTimeString());
    }
}
