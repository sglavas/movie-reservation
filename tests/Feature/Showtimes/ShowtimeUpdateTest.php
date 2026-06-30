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

    private function generateRequestData(int $movie, int $screen, int $theater, string $date, string $time, ?int $subtitles = 1, ?int $is_3d = 0, ?int $dubbed = 0): array
    {
        return[
            'movie' => $movie,
            'theater' => $theater,
            'screen' => $screen,
            'date' => $date,
            'time' => $time,
            'subtitles' => $subtitles,
            'is_3d' => $is_3d,
            'dubbed' => $dubbed,
        ];
    }

    private function generateDatabaseData(int $movie, int $screen, string $startTime, ?int $subtitles = 1, ?int $is_3d = 0, ?int $dubbed = 0): array
    {
        return [
            'id' => $this->showtime->id,
            'movie_id' => $movie,
            'screen_id' => $screen,
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
        // Assert that the showtime was updated
        $this->assertDatabaseHas('showtimes', $expectedDbData);
        // Assert redirect
        $response->assertRedirect("/showtimes/{$this->showtime->id}");
        // Assert success flash message
        $response->assertSessionHas('inertia.flash_data.success', 'Showtime updated successfully');
    }

    private function submitUpdateAndAssertFailure(array $requestData, array $expectedDbData, array $originalDbData): void
    {
        /* ACT */
        // Submit a PATCH request from /showtimes/:id/edit
        $response = $this->from("/showtimes/{$this->showtime->id}/edit")
                         ->patch("/showtimes/{$this->showtime->id}", $requestData);


        /* ASSERT */
        // Assert that the showtime was not updated
        $this->assertDatabaseMissing('showtimes', $expectedDbData);
        $this->assertDatabaseHas('showtimes', $originalDbData);
        // Assert redirect
        $response->assertRedirect("/showtimes/{$this->showtime->id}/edit");
        // Assert success flash message
        $response->assertSessionHasErrors(['time']);
    }

    #[DataProvider('updateShowtimeDataProvider')]
    public function test_update_showtime(string $requestKey, string|int $requestValue, string $dbKey, string|int $dbValue): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestData($this->movie->id, $this->screen->id, $this->screen->theater->id, '2026-02-17', '13:30');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie->id, $this->screen->id, '2026-02-17 13:30:00');

        $requestData[$requestKey] = $requestValue;
        $updatedShowtime[$dbKey] = $dbValue;

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public static function updateShowtimeDataProvider(): array
    {
        return [
            'update start time' => ['time', '14:00', 'start_time', '2026-02-17 14:00:00'],
            'update start date' => ['date', '2026-02-18', 'start_time', '2026-02-18 13:30:00'],
            'update subtitles' => ['subtitles', 0, 'subtitles', 0],
            'update is_3d' => ['is_3d', 1, 'is_3d', 1],
            'update dubbed' => ['dubbed', 1, 'dubbed', 1],
        ];
    }


    public function test_update_movie(): void
    {
        /* ARRANGE */
        // Create new movie
        $newMovie = Movie::factory()->create();
        $requestData = $this->generateRequestData($newMovie->id, $this->screen->id, $this->screen->theater->id, '2026-02-17', '13:30');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($newMovie->id, $this->screen->id, '2026-02-17 13:30:00');

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_update_screen(): void
    {
        /* ARRANGE */
        // Create a new screen in the same theater
        $secondScreen = Screen::factory()->for($this->screen->theater)->create();
        $requestData = $this->generateRequestData($this->movie->id, $secondScreen->id, $secondScreen->theater->id, '2026-02-17', '13:30');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie->id, $secondScreen->id, '2026-02-17 13:30:00');

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    public function test_full_update(): void
    {
        /* ARRANGE */
        // Create new movie
        $newMovie = Movie::factory()->create();
        // Create a new screen in the same theater
        $secondScreen = Screen::factory()->for($this->screen->theater)->create();
        $requestData = $this->generateRequestData($newMovie->id, $secondScreen->id, $secondScreen->theater->id, '2026-02-19', '15:00', subtitles: 0, is_3d: 1, dubbed: 1);
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($newMovie->id, $secondScreen->id, '2026-02-19 15:00:00', subtitles: 0, is_3d: 1, dubbed: 1);

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
    }

    #[DataProvider('showtimeProvider')]
    public function test_showtime_cannot_be_updated(string $startTime, string $startDate): void
    {
        /* ARRANGE */
        // Create second showtime
        $this->createShowtime('2026-02-17 16:00:00');
        $requestData = $this->generateRequestData($this->movie->id, $this->screen->id, $this->screen->theater->id,'2026-02-17', $startTime);
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie->id, $this->screen->id, $startDate);
        $originalData = $this->generateDatabaseData($this->showtime->movie_id, $this->showtime->screen_id, $this->showtime->start_time);

        $this->submitUpdateAndAssertFailure($requestData, $updatedShowtime, $originalData);
    }

    public static function showtimeProvider(): array
    {
        return [
            'new start time cannot be exactly the same as another showtime' => ['16:00', '2026-02-17 16:00:00'],
            'showtime cannot start during another showtime' => ['16:15', '2026-02-17 16:15:00'],
            'showtime cannot end during another showtime' => ['15:00', '2026-02-17 15:00:00'],
            'showtime cannot start 1 minute before second showtime ends' => ['17:59', '2026-02-17 17:59:00'],
        ];
    }

    public function test_it_starts_exactly_when_another_showtime_ends(): void
    {
        /* ARRANGE */
        // Create second showtime
        $this->createShowtime('2026-02-17 16:00:00');
        // Second showtime lasts from 16:00 to 18:00. The updated showtime should start at 18:00 and end at 20:00.
        $requestData = $this->generateRequestData($this->movie->id, $this->screen->id, $this->screen->theater->id, '2026-02-17', '18:00');
        // Expected DB Data
        $updatedShowtime = $this->generateDatabaseData($this->movie->id, $this->screen->id, '2026-02-17 18:00:00');

        $this->submitUpdateAndAssertSuccess($requestData, $updatedShowtime);
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
        $requestData = $this->generateRequestData($newMovie->id, $secondScreen->id, $secondScreen->theater->id,'2026-02-19', '15:00', subtitles: 0, is_3d: 1, dubbed: 1);

        // Existing showtime
        $existingShowtime = $this->generateDatabaseData($this->movie->id, $this->screen->id, '2026-02-17 13:30:00');

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
        $requestData = $this->generateRequestData($this->movie->id, $this->screen->id, $this->screen->theater->id, '2026-02-17', '14:00');
        $originalShowtime = $this->generateDatabaseData($this->movie->id, $this->screen->id, '2026-02-17 13:30:00');
        $updatedShowtime = $this->generateDatabaseData($this->movie->id, $this->screen->id, '2026-02-17 14:00:00');

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

    public function test_regular_user_cannot_update_showtime(): void
    {
        /* ARRANGE */
        $user = User::factory()->create();
        $this->actingAs($user);
        $requestData = $this->generateRequestData($this->movie->id, $this->screen->id, $this->screen->theater->id, '2026-02-17', '14:00');
        $originalShowtime = $this->generateDatabaseData($this->movie->id, $this->screen->id, '2026-02-17 13:30:00');
        $updatedShowtime = $this->generateDatabaseData($this->movie->id, $this->screen->id, '2026-02-17 14:00:00');

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
