<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,

            'hourly_rate' => $this->hourly_rate,

            'price_1_hour' => $this->price_1_hour,
            'price_3_hours' => $this->price_3_hours,
            'price_5_hours' => $this->price_5_hours,
            'price_night' => $this->price_night,

            'prices' => [
                '1_hour' => $this->price_1_hour,
                '3_hours' => $this->price_3_hours,
                '5_hours' => $this->price_5_hours,
                'night' => $this->price_night,
            ],

            'description' => $this->description,
        ];
    }
}
