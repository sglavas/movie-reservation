<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Theater;

class Screen extends Model
{
    /** @use HasFactory<\Database\Factories\ScreenFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'theater_id',
        'name',
        'regular_seats',
        'couples_seats',
        'vip_seats',
        'disability_seats',
        'royal_seats',
        'total_seats'
    ];

    public function theater() {
        return $this->belongsTo(Theater::class);
    }

    public function seats() {
        return $this->hasMany(Seat::class);
    }
}
