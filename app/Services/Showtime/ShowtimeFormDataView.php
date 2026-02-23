<?php

namespace App\Services\Showtime;

use Illuminate\Support\Collection;

class ShowtimeFormDataView
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Creates a collection of showtimes using screen IDs as keys for ShowtimeTimetable component
     * 
     * 
     * Return data format:
     * Collection {
     *      screen_id (int) => [
     *          [
     *              'id' => int,
     *              'movie_id' => int,
     *              'screen_id' => int,
     *              'movie_title' => string,
     *              'start_time' => string,
     *              'end_time' => string,
     *              'date' => string,
     *              'end_date' => string,
     *              'subtitles' => bool,
     *              'is_3d' => bool,
     *              'dubbed' => bool,
     *          ],
     *      ],
     * }
     *
     * @param Collection $movieWithId Lookup Collection (Key: movie_id, Value: Movie Model)
     * @param Collection $screensWithShowtimes Screen collection with their corresponding showtimes
     * @return Collection Returns a collection of showtimes ready for front-end consumption using screen IDs as keys
     */
    private function createKeysArray(Collection $movieWithId, Collection $screensWithShowtimes): Collection
    {
        // Create an associative array of showtimes using screen IDs as keys for ShowtimeTimetable component
        $showtimesOrderedbyScreen = $screensWithShowtimes->mapWithKeys(function ($screen) use($movieWithId) {
            $showtimeWithMovieName = $screen->showtimes->map(function ($showtime) use($movieWithId) {
                $startTimeDate = date_create($showtime->start_time);
                $endTimeDate = date_create($showtime->end_time);

                return[
                    'id' => $showtime->id,
                    'movie_id' => $showtime->movie_id,
                    'screen_id' => $showtime->screen_id,
                    'movie_title' => $movieWithId[$showtime->movie_id]->title,
                    'start_time' => $startTimeDate->format('H:i'),
                    'end_time' => $endTimeDate->format('H:i'),
                    'date' => $startTimeDate->format('Y-m-d'),
                    'end_date' => $endTimeDate->format('Y-m-d'),
                    'subtitles' => $showtime->subtitles,
                    'is_3d' => $showtime->is_3d,
                    'dubbed' => $showtime->dubbed,

                ];
            });
            return [$screen->id => $showtimeWithMovieName];
        });

        return $showtimesOrderedbyScreen;
    }

    /**
     * Shape the data for the ShowtimeTimetable component
     * 
     * Transforms raw database collections into a frontend-friendly structure
     * that groups showtimes by screen with human-readable screen names.
     * 
     * Return data format:
     * [
     *      [
     *          'screen_id' => int,
     *          'screen_name' => string (e.g. Joker(Split), Screen 1)
     *          'showtimes' => [
     *                [
     *                     'id' => int, 
     *                     'movie_id' => int,
     *                     'screen_id' => int,
     *                     'movie_title' => string,
     *                     'start_time' => string,
     *                     'end_time' => string,
     *                     'dubbed' => bool,
     *                     'subtitles' => bool,
     *                     'is_3d' => bool
     *                 ],
     *           ],
     *      ],
     * ]  
     * 
     *
     * @param Collection $screens All database screens containing id, theater_id and label
     * @param Collection $filteredShowtimes Filtered collection of showtimes using screen ID as key, containing only screens with showtimes
     * @param Collection $theaterWithId Collection using theater IDs as keys and theater name and city as values
     * 
     * @note Assumes showtimes inside $filteredShowtimes are already formatted for frontend consumption
     * 
     * @return array Returns an array ready to use in the ShowtimeTimetable component
     */
    private function shapeTimetableData(Collection $screens, Collection $filteredShowtimes, Collection $theaterWithId): array
    {
        $timetableData = $screens->filter(function ($screen) use($filteredShowtimes) {
            // Use only screen IDs that correspond to existing showtimes
            return isset($filteredShowtimes[$screen->id]);
        })->map(function ($screen) use($theaterWithId, $filteredShowtimes) {
                ['name' => $name, 'city' => $city] = $theaterWithId[$screen->theater_id];
                return[
                    'screen_id' => $screen->id,
                    'screen_name' => "$name($city), Screen $screen->label",
                    'showtimes' => $filteredShowtimes[$screen->id],
                ];
        })->values()->all();                       // Reset the keys to consecutive integers and return the array

        return $timetableData;
    }


    /**
     * Shapes data for ShowtimeInformation and ShowtimeAttributes components
     * 
     * Return data format:
     * [
     *          'screens' => [
     *                  [
     *                      'id' => int,
     *                      'theater_id' => int,
     *                      'label' => int,
     *                      'theater_name' => string,
     *                      'city' => string,
     *                  ],
     *            ],
     *            'movies' => [
     *                   [
     *                      'id' => int,
     *                      'title' => string,
     *                      'duration' => int,
     *                      'description' => string,
     *                      'genre' => string,
     *                   ],
     *            ]
     * ]
     * 
     *
     * @param Collection $screens All database screens containing id, theater_id and label
     * @param Collection $movieWithId (Key: movie_id, Value: Movie Model)
     * @param Collection $theaterWithId Lookup Collection (Key: theater_id, Value: Theater Model)
     * 
     * @return array Returns an array ready to use in ShowtimeInformation and ShowtimeAttributes components
     * 
     * @note Assumes $movieWithId and $theaterWithId are keyed by their respective IDs.
     */
    private function shapeShowtimeInformation(Collection $screens, Collection $movieWithId, Collection $theaterWithId): array
    {
        $screensWithTheaters = $screens->map(function ($screen) use($theaterWithId){
            // Destructure the screen array
            ['id' => $id, 'theater_id' => $theater_id, 'label' => $label] = $screen;

            $screenInfo = [
                'id' => $id,
                'theater_id' => $theater_id,
                'label' => $label,
                'theater_name' => $theaterWithId[$theater_id]->name,
                'city' => $theaterWithId[$theater_id]->city,
            ];

            return $screenInfo;
        });


        return[
            'screens' => $screensWithTheaters,
            'movies' => $movieWithId->values()
        ];

    }

    /**
     * Orchestrates the transformation of movies, theaters, and screens data for display.
     * 
     * Return format:
     * [
     *      'timeTableData' => Collection,
     *      'screensWithTheaters' => array
     * ]
     *
     * @param Collection $movies Movie collection containing ID, title, duration, description and genre
     * @param Collection $theaters Theater collection containing ID, name and city
     * @param Collection $screens Screen collection containing ID, theater ID and label
     * @return array Returns an array containing timetableData for the ShowtimeTimetable component, and screensWithTheaters for the ShowtimeInformation and ShowtimeAttributes components
     */
    public function shapeData(Collection $movies, Collection $theaters, Collection $screens): array
    { 
        // Turn the collection into an associative array using movie IDs as keys for fast O(1) lookup
        $movieWithId = $movies->mapWithKeys(function ($movie) {
            return [$movie->id => $movie];
        });

        // Get an associative array using theater IDs as keys and theater name and city as values
        $theaterWithId = $theaters->mapWithKeys(function ($theater) {
            return [$theater->id => $theater];
        });

        // Create an associative array of showtimes using screen IDs as keys for ShowtimeTimetable component
        $showtimesOrderedbyScreen = $this->createKeysArray($movieWithId, $screens);

        // Filter out the screens with no showtimes
        $filteredShowtimes = $showtimesOrderedbyScreen->filter(function ($showtime){
            if(sizeof($showtime) > 0){
                return $showtime;
            }
        });


        // Shape the data to be used in the ShowtimeTimetable component
        $timetableData = $this->shapeTimetableData($screens, $filteredShowtimes, $theaterWithId);

        // Shape data for ShowtimeInformation and ShowtimeAttributes components
        $screensWithTheaters = $this->shapeShowtimeInformation($screens, $movieWithId, $theaterWithId);

        return [
            'timeTableData' => $timetableData,
            'screensWithTheaters' => $screensWithTheaters,
        ];
           
    }
}
