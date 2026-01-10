<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;
use App\Models\Screen;

class Seat extends Model
{
    protected $fillable = [
        'screen_id',
        'row',
        'number',
        'type',
    ];

    public function screen() {
        return $this->belongsTo(Screen::class);
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }

}
