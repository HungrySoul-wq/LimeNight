<?php

namespace Database\Factories;

use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZoneFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'hourly_rate' => 500,
            'price_1_hour' => 500,
            'price_3_hours' => 1300,
            'price_5_hours' => 2000,
            'price_night' => 3500,
            'description' => fake()->sentence(),
        ];
    }
}