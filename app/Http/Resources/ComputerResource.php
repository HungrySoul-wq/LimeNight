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
            'status' => $this->status, // available, occupied, maintenance
            'specs' => $this->specs,
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'games' => GameResource::collection($this->whenLoaded('games')),
        ];
    }
}