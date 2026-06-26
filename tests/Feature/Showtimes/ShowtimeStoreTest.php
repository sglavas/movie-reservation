<?php

namespace Tests\Feature\Showtimes;

use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;


class ShowtimeStoreTest extends TestCase
{
    use RefreshDatabase;

    private Movie $movie;
    private Collection $screens;
    private array $request;
    private User $admin;
    private Screen $screen;

    protected function setUp(): void
    {
        parent::setUp();

        $this->movie = Movie::factory()->create();

        $this->screens = Screen::factory(6)
                ->forTheater([
                    'total_screens' => 6
                ])
                ->create();

        $this->screen = $this->screens->first();

        $this->admin = User::factory()->create([
            'is_admin' => true,
        ]);

        // Perfect base request
        $this->request = [
                            'movie' => $this->movie->id,
                            'theater' => $this->screen->theater->id,
                            'screen' => $this->screen->id,                          
                            'date' => '2026-02-17',
                            'time' => '13:30',
                            'subtitles' => 1,
                            'is_3d' => 0,
                            'dubbed' => 0,          
        ];
        
        $this->actingAs($this->admin);
    }

    private function createFirstShowtime(): void
    {
        Showtime::factory()->state([
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen->id,
            'start_time' => '2026-02-17 13:30:00',
            'subtitles' => 1,
            'is_3d' => 0,
            'dubbed' => 0,
        ])->create();
    }

    private function generateRequestdata(string $date, string $time): array
    {
        return[
            'movie' => $this->movie->id,                 
            'theater' => $this->screen->theater->id,                         
            'screen' => $this->screen->id,                          
            'date' => $date,
            'time' => $time,                    
            'subtitles' => 1,      
            'is_3d' => 0,     
            'dubbed' => 0,    
        ];
    }

    private function generateDatabaseData(string $startTime): array
    {
        return [
            'movie_id' => $this->movie->id,
            'screen_id' => $this->screen->id,
            'start_time' => $startTime,
        ];
    }

    private function submitFormAndAssertSuccess(array $requestData, array $expectedData): void
    {
        /* ACT */
        // Submit a POST request from /showtimes/create
        $response = $this->from('/showtimes/create')
                         ->post('/showtimes', $requestData);

        /* ASSERT */
        // Assert redirect
        $response->assertRedirect('/showtimes/create');
        // Assert success flash message
        $response->assertSessionHas('inertia.flash_data.success', 'Showtime created successfully');
        // Assert that showtime was persisted to the database
        $this->assertDatabaseHas('showtimes', $expectedData);
    }

    private function submitFormAndAssertFailure(array $requestData, string $errorKey, array $firstShowtimeData, array $secondShowtimeData): void
    {
        /* ACT */
        // Create the second showtime by submitting a POST request
        $response = $this->from('/showtimes/create')
                        ->post('/showtimes', $requestData);

        /* ASSERT */
        // Assert response error with time key
        $response->assertSessionHasErrors([$errorKey]);
        // Assert redirect back to the page
        $response->assertRedirect('/showtimes/create');
        // Assert the first showtime has been added to the database
        $this->assertDatabaseHas('showtimes', $firstShowtimeData);
        // Assert the second showtime has not been added to the database
        $this->assertDatabaseMissing('showtimes', $secondShowtimeData);

    }

    public function test_create_showtime(): void
    {
        /* ARRANGE */
        $requestData = $this->generateRequestdata('2026-02-17', '13:00');
        $expectedData = $this->generateDatabaseData('2026-02-17 13:00:00');

        $this->submitFormAndAssertSuccess($requestData, $expectedData);
    }

    #[DataProvider('overLappingShowtimeProvider')]
    public function test_cannot_store_overlapping_showtime(string $startTime, string $time): void
    {
        /* ARRANGE */
        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime();
        // Second showtime starts at 14:00 and ends at 16:00
        $requestData = $this->generateRequestdata('2026-02-17', $time);
        // First showtime data that should be in the database
        $firstShowtimeData = $this->generateDatabaseData('2026-02-17 13:30:00');
        // Second showtime data that should be in the database
        $secondShowtimeData = $this->generateDatabaseData($startTime);

        $this->submitFormAndAssertFailure($requestData, 'time', $firstShowtimeData, $secondShowtimeData);

    }

    public static function overLappingShowtimeProvider(): array
    {
        return [
            'start during existing showtime' => ['2026-02-17 14:00:00', '14:00'],
            'end during existing showtime' => ['2026-02-17 12:00:00', '12:00'],
            'start one minute before existing showtime ends' => ['2026-02-17 15:29:00', '15:29'],
        ];
    }

    public function test_showtime_cannot_fully_cover_existing_showtime(): void
    {
        /* ARRANGE */
        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime();
        // Second showtime starts at 13:30 and ends at 15:30
        $requestData = $this->generateRequestdata('2026-02-17', '13:30');
        // First showtime data that should be in the database
        $firstShowtimeData = $this->generateDatabaseData('2026-02-17 13:30:00');

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

    #[DataProvider('showtimeProvider')]
    public function test_can_store_showtimes_that_do_not_overlap (string $startTime, string $time): void
    {
        /* ARRANGE */
        // Create first showtime starting at 13:30 and ending at 15:30
        $this->createFirstShowtime();
        // Second showtime starts at 15:30 and ends at 17:30
        $secondShowtimeData = $this->generateRequestdata('2026-02-17', $time);
        // Second showtime data that should be in the database
        $expectedData = $this->generateDatabaseData($startTime);

        $this->submitFormAndAssertSuccess($secondShowtimeData, $expectedData);

    }

    public static function showtimeProvider(): array
    {
        return [
            'new showtime starts exactly when first showtime ends' => ['2026-02-17 15:30:00', '15:30'],
            'new showtime starts 1 minute after first showtime ends' => ['2026-02-17 15:31:00', '15:31'],
        ];
    }

    #[DataProvider('invalidDataProvider')]
    public function test_validation_rules_for_showtime_store(string $field, string|null $value): void
    {
        /* ARRANGE */
        if(is_null($value)){
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

    public function test_guest_cannot_create_showtime(): void
    {
        /* ARRANGE */
        $this->actingAsGuest();

        /* ACT */
        // Create a showtime by submitting a POST request from /showtimes/create
        $response = $this->from('/showtimes/create')
                         ->post('/showtimes', $this->request);

        /* ASSERT */
        // Assert redirect
        $response->assertRedirect('/login');
        // Assert database contains no showtimes
        $this->assertEmpty(Showtime::all());
    }

    public function test_regular_user_cannot_create_showtime(): void
    {
        /* ARRANGE */
        $user = User::factory()->create();
        $this->actingAs($user);

        /* ACT */
        // Create a showtime by submitting a POST request from /showtimes/create
        $response = $this->from('/showtimes/create')
                         ->post('/showtimes', $this->request);

        /* ASSERT */
        // Assert redirect
        $response->assertForbidden();
        // Assert database contains no showtimes
        $this->assertEmpty(Showtime::all());
    }

}
