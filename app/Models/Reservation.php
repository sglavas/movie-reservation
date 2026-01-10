<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use App\Models\Seat;
use App\Models\Showtime;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'showtime_id',
        'seat_id',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function seat() {
        return $this->belongsTo(Seat::class);
    }

    public function showtime() {
        return $this->belongsTo(Showtime::class);
    }
}
