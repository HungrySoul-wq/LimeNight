<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Seeder;

class GameSeeder extends Seeder
{
    public function run(): void
    {
        $games = [
            [
                'title' => 'Counter-Strike 2',
                'genre' => 'Shooter',
                'min_age_rating' => 16,
                'icon_url' => null,
            ],
            [
                'title' => 'Dota 2',
                'genre' => 'MOBA',
                'min_age_rating' => 12,
                'icon_url' => null,
            ],
            [
                'title' => 'Valorant',
                'genre' => 'Shooter',
                'min_age_rating' => 16,
                'icon_url' => null,
            ],
            [
                'title' => 'PUBG: Battlegrounds',
                'genre' => 'Battle Royale',
                'min_age_rating' => 16,
                'icon_url' => null,
            ],
            [
                'title' => 'Fortnite',
                'genre' => 'Battle Royale',
                'min_age_rating' => 12,
                'icon_url' => null,
            ],
            [
                'title' => 'Apex Legends',
                'genre' => 'Battle Royale',
                'min_age_rating' => 16,
                'icon_url' => null,
            ],
            [
                'title' => 'GTA V',
                'genre' => 'Action',
                'min_age_rating' => 18,
                'icon_url' => null,
            ],
            [
                'title' => 'Minecraft',
                'genre' => 'Sandbox',
                'min_age_rating' => 6,
                'icon_url' => null,
            ],
            [
                'title' => 'League of Legends',
                'genre' => 'MOBA',
                'min_age_rating' => 12,
                'icon_url' => null,
            ],
            [
                'title' => 'EA Sports FC',
                'genre' => 'Sport',
                'min_age_rating' => 0,
                'icon_url' => null,
            ],
        ];

        foreach ($games as $game) {
            Game::updateOrCreate(
                ['title' => $game['title']],
                $game
            );
        }
    }
}