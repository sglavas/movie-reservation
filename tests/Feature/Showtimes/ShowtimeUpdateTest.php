<?php

namespace Tests\Feature\Showtimes;

use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ShowtimeUpdateTest extends TestCase
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

    private function generateRequestData(Movie $movie, Screen $screen, string $date, string $time, ?int $subtitles = 1, ?int $is_3d = 0, ?int $dubbed = 0): array
    {
        return[
            'movie' => $movie->id,
            'theater' => $screen->theater->id,
            'screen' => $screen->id,
            'date' => $date,
            'time' => $time,
            'subtitles' => $subtitles,
            'is_3d' => $is_3d,
            'dubbed' => $dubbed,
        ];
    }

    private function generateDatabaseData(Movie $movie, Screen $screen, string $startTime, ?int $subtitles = 1, ?int $is_3d = 0, ?int $dubbed = 0): array
    {
        return [
            'id' => $this->showtime->id,
            'movie_id' => $movie->id,
            'screen_id' => $screen->id,
            'start_time' => $startTime,
            'subtitles' => $subtitles,
            'is_3d' => $is_3d,
            'dubbed' => $dubbed,
        ];
    }

    private function submitUpdateAndAssertSuccess(array $requestData, array $expectedDbData): void
    {
        /* ACT */
        // Submit a PATCH request from /showtimes/:id/edit
        $response = $this->from("/showtimes/{$this->showtime->id}/edit")
                         ->patch("/showtimes/{$this->showtime->id}", $requestData);

        /* ASSERT */
        // Assert that the showtime was not updated
        $this->assertDatabaseHas('showtimes', $expectedDbData);
        // Assert redirect
        $response->assertRedirect("/showtimes/{$this->showtime->id}");
        // Assert success flash message
        $response->assertSessionHas('inertia.flash_data.success', 'Showtime updated successfully');
    }

    private function submitUpdateAndAssertFailure(array $requestData, array $expectedDbData): void
    {
        /* ACT */
        // Submit a PATCH request from /showtimes/:id/edit
        $response = $this->from("/showtimes/{$this->showtime->id}/edit")
                         ->patch("/showtimes/{$this->showtime->id}", $requestData);


        /* ASSERT */
        // Assert that the showtime was updated
        $this->assertDatabaseMissing('showtimes', $expectedDbData);
        // Assert redirect
        $response->assertRedirect("/showtimes/{$this->showtime->id}/edit");
        // Assert success flash message
        $response->assertSessionHasErrors(['time']);
    }

    public function test_update_start_time(): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '14:00');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 14:00:00');

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_update_start_date(): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-18', '13:30');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-18 13:30:00');

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_update_movie(): void
    {
        /* ARRANGE */
        // Create new movie
        $newMovie = Movie::factory()->create();
        $requestData = $this->generateRequestData($newMovie, $this->screen,'2026-02-17', '13:30');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($newMovie, $this->screen, '2026-02-17 13:30:00');

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_update_screen(): void
    {
        /* ARRANGE */
        // Create a new screen in the same theater
        $secondScreen = Screen::factory()->for($this->screen->theater)->create();
        $requestData = $this->generateRequestData($this->movie, $secondScreen,'2026-02-17', '13:30');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $secondScreen, '2026-02-17 13:30:00');

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_update_subtitles(): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '13:30', subtitles: 0);
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 13:30:00', subtitles: 0);

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_update_is_3d(): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '13:30', is_3d: 1);
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 13:30:00', is_3d: 1);

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_update_dubbed(): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '13:30', dubbed: 1);
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 13:30:00', dubbed: 1);

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_full_update(): void
    {
        /* ARRANGE */
        // Create new movie
        $newMovie = Movie::factory()->create();
        // Create a new screen in the same theater
        $secondScreen = Screen::factory()->for($this->screen->theater)->create();
        $requestData = $this->generateRequestData($newMovie, $secondScreen,'2026-02-19', '15:00', subtitles: 0, is_3d: 1, dubbed: 1);
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($newMovie, $secondScreen, '2026-02-19 15:00:00', subtitles: 0, is_3d: 1, dubbed: 1);

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_it_fails_when_start_time_is_exactly_the_same_as_another_showtime(): void
    {
        /* ARRANGE */
        // Create second showtime
        $this->createShowtime('2026-02-17 16:00:00');
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '16:00');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 16:00:00');

        $this->submitUpdateAndAssertFailure($requestData, $updatedShowtime);
    }
    
    public function test_it_fails_when_new_time_starts_during_another_showtime(): void
    {
        /* ARRANGE */
        // Create second showtime
        $this->createShowtime('2026-02-17 16:00:00');
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '16:15');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 16:15:00');

        $this->submitUpdateAndAssertFailure($requestData, $updatedShowtime);
    }

    public function test_it_fails_when_new_time_ends_after_another_showtime_has_already_started(): void
    {
        /* ARRANGE */
        // Create second showtime
        $this->createShowtime('2026-02-17 16:00:00');
        // Movie factory duration 90 min + 30 min buffer = 2 hours
        // Second showtime lasts from 16:00 to 18:00. The updated showtime should start at 15:00 and end at 17:00.
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '15:00');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 15:00:00');

        $this->submitUpdateAndAssertFailure($requestData, $updatedShowtime);
    }

    public function test_it_starts_exactly_when_another_showtime_ends(): void
    {
        /* ARRANGE */
        // Create second showtime
        $this->createShowtime('2026-02-17 16:00:00');
        // Second showtime lasts from 16:00 to 18:00. The updated showtime should start at 18:00 and end at 20:00.
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '18:00');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 18:00:00');

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_it_fails_when_new_time_starts_1_minute_before_second_showtime_ends (): void
    {
        /* ARRANGE */
        // Create second showtime
        $this->createShowtime('2026-02-17 16:00:00');
        // Second showtime lasts from 16:00 to 18:00. The updated showtime should start at 17:59 and end at 19:59.
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '17:59');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 17:59:00');

        $this->submitUpdateAndAssertFailure($requestData, $updatedShowtime);
    }

    #[DataProvider('invalidDataProvider')]
    public function test_validation_rules_for_showtime_update(string $field, string|null $value): void
    {
        /* ARRANGE */
        // Create new movie
        $newMovie = Movie::factory()->create();
        // Create a new screen in the same theater
        $secondScreen = Screen::factory()->for($this->screen->theater)->create();
        // Full update data
        $requestData = $this->generateRequestData($newMovie, $secondScreen,'2026-02-19', '15:00', subtitles: 0, is_3d: 1, dubbed: 1);

        // Existing showtime
        $existingShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 13:30:00');

        if(is_null($value)){
            // Unset field and submit form without the value
            unset($requestData[$field]);
        }else{
            // Set field to invalid value
            $requestData[$field] = $value;
        }

        /* ACT */
        // Create a showtime by submitting a POST request from /showtimes/create
        $response = $this->from("/showtimes/{$this->showtime->id}/edit")
                        ->patch("/showtimes/{$this->showtime->id}", $requestData);

        /* ASSERT */
        // Assert response error with field key
        $response->assertSessionHasErrors([$field]);
        // Assert redirect
        $response->assertRedirect("/showtimes/{$this->showtime->id}/edit");
        // Assert that the showtime was not updated
        $this->assertDatabaseHas('showtimes', $existingShowtime);
    }

    public static function invalidDataProvider(): array
    {
        return [
            // Movie
            'empty movie' => ['movie', ''],
            'invalid movie' => ['movie', '500'],
            'missing movie' => ['movie', null],
            // Theater
            'empty theater' => ['theater', ''],
            'invalid theater' => ['theater', '500'],
            'missing theater' => ['theater', null],
            // Screen
            'empty screen' => ['screen', ''],
            'invalid screen' => ['screen', '500'],
            'missing screen' => ['screen', null],
            // Date
            'empty date' => ['date', ''],
            'invalid date' => ['date', 'invalid'],
            'missing date' => ['date', null],
            // Time
            'empty time' => ['time', ''],
            'invalid time' => ['time', 'invalid'],
            'missing time' => ['time', null],
            // Subtitles
            'empty subtitles' => ['subtitles', ''],
            'invalid subtitles' => ['subtitles', 'invalid'],
            'missing subtitles' => ['subtitles', null],
            // is_3d
            'empty is_3d' => ['is_3d', ''],
            'invalid is_3d' => ['is_3d', 'invalid'],
            'missing is_3d' => ['is_3d', null],
            // Dubbed
            'empty dubbed' => ['dubbed', ''],
            'invalid dubbed' => ['dubbed', 'invalid'],
            'missing dubbed' => ['dubbed', null],
        ];
    }

    public function test_guest_cannot_update_showtime(): void
    {
        /* ARRANGE */
        $this->actingAsGuest();
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '14:00');
        $originalShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 13:30:00');
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 14:00:00');

        /* ACT */
        // Submit a PATCH request from /showtimes/:id/edit
        $response = $this->from("/showtimes/{$this->showtime->id}/edit")
                         ->patch("/showtimes/{$this->showtime->id}", $requestData);

        /* ASSERT */
        // Assert that the showtime was not updated
        $this->assertDatabaseMissing('showtimes', $updatedShowtime);
        $this->assertDatabaseHas('showtimes', $originalShowtime);
        $response->assertRedirect('/login');
    }

    public function test_regular_user_cannot_updated_showtime(): void
    {
        /* ARRANGE */
        $user = User::factory()->create();
        $this->actingAs($user);
        $requestData = $this->generateRequestData($this->movie, $this->screen,'2026-02-17', '14:00');
        $originalShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 13:30:00');
        $updatedShowtime = $this->generateDatabaseData($this->movie, $this->screen, '2026-02-17 14:00:00');

        /* ACT */
        // Submit a PATCH request from /showtimes/:id/edit
        $response = $this->from("/showtimes/{$this->showtime->id}/edit")
                         ->patch("/showtimes/{$this->showtime->id}", $requestData);

        /* ASSERT */
        // Assert that the showtime was not updated
        $this->assertDatabaseMissing('showtimes', $updatedShowtime);
        $this->assertDatabaseHas('showtimes', $originalShowtime);
        $response->assertForbidden();
    }
}
