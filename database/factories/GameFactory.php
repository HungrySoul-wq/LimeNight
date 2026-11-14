<?php

namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Game>
 */
class GameFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(3, true),
            'genre' => fake()->randomElement([
                'Action',
                'RPG',
                'Strategy',
                'Shooter',
                'Sport',
            ]),
            'min_age_rating' => fake()->randomElement([0, 6, 12, 16, 18]),
            'icon_url' => null,
        ];
    }
}