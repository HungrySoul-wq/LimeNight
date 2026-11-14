<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'genre' => $this->genre,
            'min_age_rating' => $this->min_age_rating,
            'icon_url' => $this->icon_url ? asset('storage/' . $this->icon_url) : null,
        ];
    }
}