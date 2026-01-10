<?php

use function Laravel\Prompts\error;
use App\Models\Movie;
use Carbon\Carbon;

/**
 * Parses a .csv file and turns it into an array.
 * 
 * @param string $fileName Path to the resource file.
 * @return array Returns a multidimensional array, or null on failure.
 */
function csvToArray($fileName) {
    // Check if file exists
    if(!file_exists($fileName)){
        error("Data could not be retrieved.");
        return;
    }

    // Map over the .csv file and extract the rows
    $rows = array_map('str_getcsv', file($fileName));

    $columnNames = array_shift($rows);

    $data = [];

    // Combine into an associative array
    foreach($rows as $row) {
        $data[] = array_combine($columnNames, $row);
    }

    return $data;

};

/**
 * Calculates the end time of a movie
 * 
 * Each showtime must include:
 * - movie_id (int)                         - The ID of the movie 
 * - screen_id (int)                        - The ID of the screen
 * - start_time (string, Y-m-d H:i:s)       - Start time in 'Y-m-d H:i:s' form
 * - end_time (string, Y-m-d H:i:s)         - End time. However, this can also be an empty string.
 * - subtitles (bool)                       - Whether the movie has subtitles.
 * - 3d (bool)                              - Whether the movie is in 3D.
 * - dubbed (bool)                          - Whether the movie is dubbed.
 * 
 * End time is calculated by adding the movie duration and a 30-minute buffer to the showtime's start_time.
 * 
 * @param array $showtimes An array of showtimes
 * @return array The updated showtimes array with 'end_time' populated with a datetime string in the 'Y-m-d H:i:s' form.
 */
function calcEndTime($showtimes) {
    $moviesToFind = collect($showtimes)->pluck('movie_id')->unique();

    // Fetches movies from the database.
    $movies = Movie::findOrFail($moviesToFind)->pluck('duration', 'id');


    // Create a collection with the calculated end times
    $calculatedColl = collect($showtimes)->map(function ($showtime) use ($movies) {
        // Add 15 minutes before and after each movie as buffer
        $timeToAdd = 30 + $movies[$showtime['movie_id']];

        $endTime = Carbon::parse($showtime['start_time'])->addMinutes($timeToAdd);

        $showtime['end_time'] = $endTime->toDateTimeString();

        return $showtime;
    });


    // Turn the collection into an array
    $finalArr = $calculatedColl->all();

    return $finalArr;
};

