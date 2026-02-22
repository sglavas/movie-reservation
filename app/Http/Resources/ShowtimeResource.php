<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowtimeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'movie_id' => $this->movie_id,
            'screen_id' => $this->screen_id,
            'start_time' => $this->start_time,
            'subtitles' => $this->subtitles,
            'is_3d' => $this->is_3d,
            'dubbed' => $this->dubbed,
        ];
    }
}
