<?php

namespace Tests\Feature\Showtimes;

use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Override;
use Tests\TestCase;

class ShowtimeDestroyTest extends TestCase
{
    use RefreshDatabase;

    private Movie $movie;
    private Screen $screen;
    private Showtime $showtime;

    protected function setUp(): void
    {
        parent::setUp();

        $this->movie = Movie::factory()->create();

        $this->screen = Screen::factory()->create();

        $this->showtime = $this->createShowtime('2026-02-17 13:30:00');
    }

    private function createShowtime(string $startTime): Showtime
    {
        return Showtime::factory()->state([
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen->id,
            'start_time' => $startTime,
            'subtitles' => 1,
            'is_3d' => 0,
            'dubbed' => 0,
        ])->create();
    }

    // private function generateExpectedData(): array
    // {
    //     return [
    //         'id' => $this->showtime->id,
    //         'movie_id' => $movie->id,
    //         'screen_id' => $screen->id,
    //         'start_time' => $startTime,
    //         'subtitles' => $subtitles,
    //         'is_3d' => $is_3d,
    //         'dubbed' => $dubbed,
    //     ];
    // }

    private function submitDestroyAndAssertSuccess(array $expectedDbData): void
    {
        /* ACT */
        // Submit a DELETE request from /showtimes/{showtime}
        $response = $this->from("/showtimes/{$this->showtime->id}")
                         ->delete("/showtimes/{$this->showtime->id}");

        // dd($response);
        /* ASSERT */
        // Assert that the showtime was deleted
        $this->assertDatabaseHas('showtimes', $expectedDbData[0]);
        // Assert redirect
        $response->assertRedirect('/showtimes/create');
        // Assert success flash message
        // $response->assertSessionHas('inertia.flash_data.success', 'Showtime updated successfully');

    }

    public function test_it_deletes_a_showtime(): void
    {
        dd(Showtime::find(1)->select('id', 'movie_id', 'screen_id', 'start_time')->get()->toArray());
        $this->submitDestroyAndAssertSuccess(Showtime::find(1)->select('id', 'movie_id', 'screen_id', 'start_time')->get()->toArray());
    }

}
