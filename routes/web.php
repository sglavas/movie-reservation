<?php

use App\Http\Controllers\ShowtimeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('HomePage');
});


Route::get('/contact', function() {
    return Inertia::render('ContactPage');
});

// Index
Route::get('/showtimes', [ShowtimeController::class, 'index']);
Route::get('/showtimes/create', [ShowtimeController::class, 'create']);
Route::post('/showtimes', [ShowtimeController::class, 'store']);
