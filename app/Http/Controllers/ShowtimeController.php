<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Http\Resources\ScreenResource;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use App\Models\Theater;
use App\Rules\ShowtimeOverlapRule;
use App\Services\Showtime\CalculateEndTimeService;
use App\Services\Showtime\MovieShowtimeScheduleView;
use App\Services\Showtime\ShowtimeFormDataView;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ShowtimeController extends Controller
{
    public function __construct(
        protected CalculateEndTimeService $calculatingService,
        protected ShowtimeOverlapRule $overlapRule,
        protected MovieShowtimeScheduleView $presenterService,
        protected ShowtimeFormDataView $formPresenterService,
    )
    {
        //
    }

    private function getFormData(): array
    {
        // Select id, title, duration, description and genre from all movies
        $movies = Movie::select('id', 'title', 'duration', 'description', 'genre')->get();
        // Select id, name and city from all theaters
        $theaters = Theater::select('id', 'name', 'city')->get();
        // Select id, theater_id and label for all screens together with corresponding showtimes to avoid N+1
        $screens = Screen::with('showtimes')->select('id', 'theater_id', 'label')->get();

        // Form data
        $shapedData = $this->formPresenterService->shapeData($movies, $theaters, $screens);

        [
            'timetableData' => $timetableData,
            'screensWithTheaters' => $screensWithTheaters
        ] = $shapedData;

        return [
            'theaters' => $theaters,
            'timetableData' => $timetableData,
            'screensWithTheaters' => $screensWithTheaters,
        ];
    }

    public function index()
    {
        // Get all screens from the database and sort them according to screen id
        $screens = Screen::all()->mapWithKeys(function ($screen) {
            return[$screen->id => new ScreenResource($screen)];
        });

        // Fetch the movies together with the corresponding showtimes and map over the array
        $movies = Movie::with('showtimes')->get()->mapWithKeys(function ($movie) {

            // Funnel movies and showtimes through the pipeline to get
            $bookableDates = $this->presenterService->usePipeline($movie);

            // Return an array with movie IDs as custom keys and an array of movie information and showtimes
            return [$movie->id => [
                'movie' => new MovieResource($movie),                           // Filter data using MovieResource
                'showtimes' => $bookableDates
            ]];
        });

        return Inertia::render('Showtimes/Index', [
            'movies' => $movies,
            'screens' => $screens,
        ]);
    }

    public function edit()
    {
        [
            'theaters' => $theaters,
            'screensWithTheaters' => $screensWithTheaters,
        ] = $this->getFormData();

        return Inertia::render('Showtimes/Edit', [
            'theaters' => $theaters,
            'formInfo' => $screensWithTheaters,
        ]);
    }

    public function create(){
        [
            'theaters' => $theaters,
            'timetableData' => $timetableData,
            'screensWithTheaters' => $screensWithTheaters,
        ] = $this->getFormData();

        return Inertia::render('Showtimes/Create', [
            'theaters' => $theaters,
            'timetables' => $timetableData,
            'formInfo' => $screensWithTheaters,
        ]);
    }


    public function store()
    {
        // Validate input
        $validateFormInfo = request()->validate([
            'movie' => ['bail', 'required', 'integer', 'exists:movies,id'],
            'theater' => ['bail', 'required', 'integer', 'exists:theaters,id'],
            'screen' => ['bail', 'required', 'integer', 'exists:screens,id'],
            'date' => ['bail', 'required', 'date'],
            'time' => ['bail', 'required', 'date_format:H:i', $this->overlapRule],              // Implment custom overlap rule
            'subtitles' => ['bail', 'required', 'bool'],
            'is_3d' => ['bail', 'required', 'bool'],
            'dubbed' => ['bail', 'required', 'bool'],
        ]);


        // Destructure the array
        [
            'movie' => $movie,
            'screen' => $screen,
            'date' => $date,
            'time' => $time,
            'subtitles' => $subtitles,
            'is_3d' => $is_3d,
            'dubbed' => $dubbed
        ] = $validateFormInfo;

        $dateTime = date('Y-m-d H:i:s', strtotime("$date $time"));

        DB::transaction(function() use($movie, $screen, $dateTime, $subtitles, $is_3d, $dubbed){
            // Create showtime
            Showtime::create([
                'movie_id' => $movie,
                'screen_id' => $screen,
                'start_time' => $dateTime,              // Combine date and time into datetime (Y-m-d H:i:s format)
                'subtitles' => $subtitles,
                'is_3d' => $is_3d,
                'dubbed' => $dubbed
            ]);
        });

        // Flash data
        Inertia::flash('success', 'Showtime created successfully');

        // Redirect
        return back();
    }
}
