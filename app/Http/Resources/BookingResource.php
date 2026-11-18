<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_type' => $this->booking_type,

            'start_time' => $this->start_time,
            'end_time' => $this->end_time,

            'total_price' => $this->total_price,
            'status' => $this->status,

            'user' => [
                'id' => $this->user?->id,
                'name' => $this->user?->name,
            ],

            'computer' => new ComputerResource(
                $this->whenLoaded('computer')
            ),

            'room' => $this->whenLoaded('room', function () {
                return [
                    'id' => $this->room?->id,
                    'name' => $this->room?->name,
                    'type' => $this->room?->type,
                    'capacity' => $this->room?->capacity,
                ];
            }),

            'created_at' => $this->created_at,
        ];
    }
}
