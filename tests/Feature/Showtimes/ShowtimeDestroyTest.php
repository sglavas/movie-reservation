<?php

namespace Tests\Feature\Showtimes;

use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowtimeDestroyTest extends TestCase
{
    use RefreshDatabase;

    private Movie $movie;
    private Screen $screen;
    private Showtime $showtime;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->movie = Movie::factory()->create();

        $this->screen = Screen::factory()->create();

        $this->showtime = $this->createShowtime('2026-02-17 13:30:00');

        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($this->admin);
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

    private function submitDestroyAndAssertSuccess(array $expectedDbData): void
    {
        /* ACT */
        // Submit a DELETE request from /showtimes/{showtime}
        $response = $this->from("/showtimes/{$this->showtime->id}")
                         ->delete("/showtimes/{$this->showtime->id}");

        /* ASSERT */
        // Assert that the showtime was deleted
        $this->assertDatabaseMissing('showtimes', $expectedDbData);
        // Assert redirect
        $response->assertRedirect('/showtimes/create');
        // Assert success flash message
        $response->assertSessionHas('inertia.flash_data.success', 'Showtime deleted successfully');

    }

    private function submitDestroyAndAssertFailure(int $invalidId): void
    {
        /* ACT */
        // Submit a DELETE request from /showtimes/{showtime}
        $response = $this->from("/showtimes/{$invalidId}")
                         ->delete("/showtimes/{$invalidId}");

        /* ASSERT */
        // Assert 404 error
        $response->assertNotFound();
        // Assert that the showtime still exists
        $this->assertDatabaseHas('showtimes', ['id' => $this->showtime->id]);
    }

    public function test_it_deletes_a_showtime(): void
    {
        $this->submitDestroyAndAssertSuccess($this->showtime->only(['id', 'movie_id', 'screen_id', 'start_time']));
    }

    public function test_it_returns_404_when_deleting_non_existing_showtime(): void
    {
        $this->submitDestroyAndAssertFailure(500);
    }
}
