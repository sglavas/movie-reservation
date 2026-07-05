<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Showtime Buffer
    |--------------------------------------------------------------------------
    |
    | This option determines the buffer between movie showtimes. It accounts for 
    | the 15 minutes before and after each showtime (30 minutes in total)
    | required for cleanup.
    | 
    | **NOTE**
    | Some of the feature tests were written specifically with a 30-minute buffer
    | in mind. Therefore, they will fail unless adjusted.
    */

    'buffer' => (int) env('SHOWTIME_BUFFER', 30),
];
