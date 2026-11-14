<?php

namespace Database\Seeders;

use App\Models\Computer;
use App\Models\Game;
use Illuminate\Database\Seeder;

class ComputerGameSeeder extends Seeder
{
    public function run(): void
    {
        $games = Game::all();

        Computer::query()->each(function (Computer $computer) use ($games) {
            $computer->games()->syncWithoutDetaching($games->modelKeys());
        });
    }
}