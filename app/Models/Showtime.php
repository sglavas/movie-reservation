<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Movie;
use App\Models\Reservation;
use App\Models\Screen;

class Showtime extends Model
{
    protected $fillable = [
        'movie_id',
        'screen_id',
        'start_time',
        'end_time',
        'subtitles',
        '3d',
        'dubbed',
    ];

    public function movie() {
        return $this->belongsTo(Movie::class);
    }

    public function screen() {
        return $this->belongsTo(Screen::class);
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
    
}
