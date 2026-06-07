<?php

use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\SessionController;
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
Route::get('/showtimes/create', [ShowtimeController::class, 'create'])->middleware(['auth', 'can:access-admin-area']);
Route::get('/showtimes/{showtime}', [ShowtimeController::class, 'show'])->middleware(['auth', 'can:access-admin-area']);
Route::get('/showtimes/{showtime}/edit', [ShowtimeController::class, 'edit'])->middleware(['auth', 'can:access-admin-area']);
Route::patch('/showtimes/{showtime}', [ShowtimeController::class, 'update'])->middleware(['auth', 'can:access-admin-area']);
Route::delete('/showtimes/{showtime}', [ShowtimeController::class, 'destroy'])->middleware(['auth', 'can:access-admin-area']);
Route::post('/showtimes', [ShowtimeController::class, 'store'])->middleware(['auth', 'can:access-admin-area']);

Route::get('/register', [RegisteredUserController::class, 'create'])->middleware(['guest']);
Route::post('/register', [RegisteredUserController::class, 'store'])->middleware(['guest']);

Route::get('/login', [SessionController::class, 'create'])->name('login')->middleware(['guest']);
Route::post('/login', [SessionController::class, 'store'])->middleware(['guest']);
Route::post('/logout', [SessionController::class, 'destroy']);
