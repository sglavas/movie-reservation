<?php

namespace App\Http\Resources\Admin;

use App\Models\Screen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowtimeDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $screen = $this->screen;

        return [
                'id' => $this->id,
                'movie' => $this->movie->title,
                'movie_id' => $this->movie_id,
                'screen' => $screen->label,
                'screen_id' => $screen->id,
                'theater' => $screen->theater->name,
                'theater_id' => $screen->theater->id,
                'city' => $screen->theater->city,
                'start_time' => Carbon::parse($this->start_time)->format('H:i'),
                'end_time' => Carbon::parse($this->end_time)->format('H:i'),
                'date' => Carbon::parse($this->start_time)->format('Y-m-d'),
                'end_date' => Carbon::parse($this->end_time)->format('Y-m-d'),
                'subtitles' => $this->subtitles,
                'dubbed' => $this->dubbed,
                'is_3d' => $this->is_3d,
        ];
    }
}
