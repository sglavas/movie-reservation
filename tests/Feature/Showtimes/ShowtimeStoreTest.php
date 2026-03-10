<?php

namespace Tests\Feature\Showtimes;

use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

use function PHPUnit\Framework\isNull;

class ShowtimeStoreTest extends TestCase
{
    use RefreshDatabase;

    private Movie $movie;
    private Collection $screens;
    private array $request;
    /**
     * A basic feature test example.
     */

    protected function setUp(): void
    {
        parent::setUp();

        $this->movie = Movie::factory()->create();

        $this->screens = Screen::factory(6)
                ->forTheater([
                    'total_screens' => 6
                ])
                ->create();

        $screen = $this->screens->first();

        // Perfect base request
        $this->request = [
                            'movie' => $this->movie->id,
                            'theater' => $screen->theater->id,
                            'screen' => $screen->id,                          
                            'date' => '2026-02-17',
                            'time' => '13:30',
                            'subtitles' => 1,
                            'is_3d' => 0,
                            'dubbed' => 0,          
        ];
        
    }

    private function createFirstShowtime(Movie $movie, Screen $screen): void
    {
        Showtime::factory()->state([
            'movie_id' => $movie->id,
            'screen_id' => $screen->id,
            'start_time' => '2026-02-17 13:30:00',
            'subtitles' => 1,
            'is_3d' => 0,
            'dubbed' => 0,
        ])->create();
    }

    private function generateRequestdata(Movie $movie, Screen $screen, string $date, string $time): array
    {
        return[
            'movie' => $movie->id,                 
            'theater' => $screen->theater->id,                         
            'screen' => $screen->id,                          
            'date' => $date,
            'time' => $time,                    
            'subtitles' => 1,      
            'is_3d' => 0,     
            'dubbed' => 0,    
        ];
    }

    private function generateDatabaseData(Movie $movie, Screen $screen, string $startTime): array
    {
        return [
            'movie_id' => $movie->id,
            'screen_id' => $screen->id,
            'start_time' => $startTime,
        ];
    }

    public function test_create_showtime(): void
    {
        /* ARRANGE */
        $movie = $this->movie;
        $screens = $this->screens;
        $screen = $screens->first();

        $requestData = $this->generateRequestdata($movie, $screen, '2026-02-17', '13:30');

        /* ACT */
        // Submit a POST request from /showtimes/create
        $response = $this->from('/showtimes/create')
                         ->post('/showtimes', $requestData);

        /* ASSERT */
        // Assert that showtime was persisted to the database
        $this->assertDatabaseHas('showtimes', [
            'movie_id' => $movie->id,                 
            'screen_id' => $screen->id,
            'start_time' => '2026-02-17 13:30:00',     
            'subtitles' => 1,      
            'is_3d' => 0,     
            'dubbed' => 0,             
        ]);

        // Assert redirect
        $response->assertRedirect('/showtimes/create');
        // Assert success flash message
        $response->assertSessionHas('inertia.flash_data.success', 'Showtime created successfully');
    }

    public function test_showtime_cannot_start_during_existing_showtime(): void
    {
        /* ARRANGE */
        $movie = $this->movie;
        $screens = $this->screens;
        $screen = $screens->first();

        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime($movie, $screen);
        // Second showtime starts at 14:00 and ends at 16:00
        $requestData = $this->generateRequestdata($movie, $screen, '2026-02-17', '14:00');
        // First showtime data that should be in the database
        $firstShowtimeData = $this->generateDatabaseData($movie, $screen, '2026-02-17 13:30:00');
        // Second showtime data that should be in the database
        $secondShowtimeData = $this->generateDatabaseData($movie, $screen, '2026-02-17 14:00:00');

        /* ACT */
        // Create the second showtime by submitting a POST request
        $response = $this->from('/showtimes/create')
                        ->post('/showtimes', $requestData);

        /* ASSERT */
        // Assert response error with time key
        $response->assertSessionHasErrors(['time']);
        // Assert redirect back to the page
        $response->assertRedirect('/showtimes/create');
        // Assert the first showtime has been added to the database
        $this->assertDatabaseHas('showtimes', $firstShowtimeData);
        // Assert the second showtime has not been added to the database
        $this->assertDatabaseMissing('showtimes', $secondShowtimeData);
    }

    public function test_showtime_cannot_end_during_existing_showtime(): void
    {
        /* ARRANGE */
        $movie = $this->movie;
        $screens = $this->screens;
        $screen = $screens->first();

        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime($movie, $screen);
        // Second showtime starts at 12:00 and ends at 12:00
        $requestData = $this->generateRequestdata($movie, $screen, '2026-02-17', '12:00');
        // First showtime data that should be in the database
        $firstShowtimeData = $this->generateDatabaseData($movie, $screen, '2026-02-17 13:30:00');
        // Second showtime data that should be in the database
        $secondShowtimeData = $this->generateDatabaseData($movie, $screen, '2026-02-17 12:00:00');
        
        /* ACT */
        // Create the second showtime by submitting a POST request
        $response = $this->from('/showtimes/create')
                        ->post('/showtimes', $requestData);

        /* ASSERT */
        // Assert response error with time key
        $response->assertSessionHasErrors(['time']);
        // Assert redirect back to the page
        $response->assertRedirect('/showtimes/create');
        // Assert the first showtime has been added to the database
        $this->assertDatabaseHas('showtimes', $firstShowtimeData);
        // Assert the second showtime has not been added to the database
        $this->assertDatabaseMissing('showtimes', $secondShowtimeData);
    }

    public function test_showtime_cannot_fully_cover_existing_showtime(): void
    {
        /* ARRANGE */
        $movie = $this->movie;
        $screens = $this->screens;
        $screen = $screens->first();

        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime($movie, $screen);
        // Second showtime starts at 13:30 and ends at 15:30
        $requestData = $this->generateRequestdata($movie, $screen, '2026-02-17', '13:30');
        // First showtime data that should be in the database
        $firstShowtimeData = $this->generateDatabaseData($movie, $screen, '2026-02-17 13:30:00');

        /* ACT */
        // Create the second showtime by submitting a POST request
        $response = $this->from('/showtimes/create')
                        ->post('/showtimes', $requestData);

        /* ASSERT */
        // Assert response error with time key
        $response->assertSessionHasErrors(['time']);
        // Assert redirect back to the page
        $response->assertRedirect('/showtimes/create');
        // Assert the first showtime has been added to the database
        $this->assertDatabaseHas('showtimes', $firstShowtimeData);
        // Assert that there is only a single showtime in the database
        $this->assertEquals(1, Showtime::count());
    }

    public function test_showtime_cannot_start_one_minute_before_existing_showtime_ends(): void
    {
        /* ARRANGE */
        $movie = $this->movie;
        $screens = $this->screens;
        $screen = $screens->first();

        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime($movie, $screen);
        // Second showtime starts at 15:29 and ends at 17:29
        $requestData = $this->generateRequestdata($movie, $screen, '2026-02-17', '15:29');
        // First showtime data that should be in the database
        $firstShowtimeData = $this->generateDatabaseData($movie, $screen, '2026-02-17 13:30:00');
        // Second showtime data that should be in the database
        $secondShowtimeData = $this->generateDatabaseData($movie, $screen, '2026-02-17 15:29:00');

        /* ACT */
        // Create the second showtime by submitting a POST request
        $response = $this->from('/showtimes/create')
                        ->post('/showtimes', $requestData);

        /* ASSERT */
        // Assert response error with time key
        $response->assertSessionHasErrors(['time']);
        // Assert redirect back to the page
        $response->assertRedirect('/showtimes/create');
        // Assert the first showtime has been added to the database
        $this->assertDatabaseHas('showtimes', $firstShowtimeData);
        // Assert the second showtime has not been added to the database
        $this->assertDatabaseMissing('showtimes', $secondShowtimeData);
    }

    public function test_showtime_can_start_exactly_when_existing_showtime_ends(): void
    {
        /* ARRANGE */
        $movie = $this->movie;
        $screens = $this->screens;
        $screen = $screens->first();

        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime($movie, $screen);
        // Second showtime starts at 15:30 and ends at 17:30
        $secondShowtimeData = $this->generateRequestdata($movie, $screen, '2026-02-17', '15:30');
        // Second showtime data that should be in the database
        $expectedData = $this->generateDatabaseData($movie, $screen, '2026-02-17 15:30:00');

        /* ACT */
        // Create the second showtime by submitting a POST request from /showtimes/create
        $response = $this->from('/showtimes/create')
                        ->post('/showtimes',$secondShowtimeData);

        /* ASSERT */
        // Assert redirect
        $response->assertRedirect('/showtimes/create');
        // Assert success flash message
        $response->assertSessionHas('inertia.flash_data.success', 'Showtime created successfully');
        // Assert that showtime was persisted to the database
        $this->assertDatabaseHas('showtimes', $expectedData);

    }

    public function test_showtime_can_start_one_minute_after_existing_showtime_ends(): void
    {
        /* ARRANGE */
        $movie = $this->movie;
        $screens = $this->screens;
        $screen = $screens->first();

        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime($movie, $screen);
        // Second showtime starts at 15:31 and ends at 17:31
        $secondShowtimeData = $this->generateRequestdata($movie, $screen, '2026-02-17', '15:31');
        // Second showtime data that should be in the database
        $expectedData = $this->generateDatabaseData($movie, $screen, '2026-02-17 15:31:00');

        /* ACT */
        // Create the second showtime by submitting a POST request from /showtimes/create
        $response = $this->from('/showtimes/create')
                        ->post('/showtimes',$secondShowtimeData);
        
        /* ASSERT */
        // Assert redirect
        $response->assertRedirect('/showtimes/create');
        // Assert success flash message
        $response->assertSessionHas('inertia.flash_data.success', 'Showtime created successfully');
        // Assert that showtime was persisted to the database
        $this->assertDatabaseHas('showtimes', $expectedData);
    }

    #[DataProvider('invalidDataProvider')]
    public function test_validation_rules_for_showtime_store(string $field, string|null $value): void
    {
        /* ARRANGE */
        if(isNull($value)){
            // Unset field and submit form without the value
            unset($this->request[$field]);
        }else{
            // Set field to invalid value
            $this->request[$field] = $value;
        }

        /* ACT */
        // Create a showtime by submitting a POST request from /showtimes/create
        $response = $this->from('/showtimes/create')
                        ->post('/showtimes', $this->request);

        /* ASSERT */
        // Assert response error with field key
        $response->assertSessionHasErrors([$field]);
        // Assert redirect
        $response->assertRedirect('/showtimes/create');
        // Assert database contains no showtimes
        $this->assertEmpty(Showtime::all());
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
}
