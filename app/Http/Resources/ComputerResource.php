<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComputerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pc_number' => $this->pc_number,
            'ip_address' => $this->ip_address,
            'status' => $this->status,
            'specs' => $this->specs,

            'zone' => new ZoneResource($this->whenLoaded('zone')),

            'room' => $this->whenLoaded('room', function () {
                return [
                    'id' => $this->room?->id,
                    'name' => $this->room?->name,
                    'type' => $this->room?->type,
                    'capacity' => $this->room?->capacity,
                    'is_active' => $this->room?->is_active,
                ];
            }),

            'bookings' => BookingResource::collection(
                $this->whenLoaded('bookings')
            ),

            'games' => GameResource::collection($this->whenLoaded('games')),
        ];
    }
}
