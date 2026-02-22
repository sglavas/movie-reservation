<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Movie;
use App\Models\Reservation;
use App\Models\Screen;
use App\Observers\ShowtimeObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Showtime extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'movie_id',
        'screen_id',
        'start_time',
        'end_time',
        'subtitles',
        'is_3d',
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

    protected static function booted()
    {
        Showtime::observe(ShowtimeObserver::class);
    }

}
