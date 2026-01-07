<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theater extends Model
{
    /** @use HasFactory<\Database\Factories\TheaterFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'city',
        'total_screens',
        'seats',
    ];


    public function screens() {
        return $this->hasMany(Screen::class);
    }

}
