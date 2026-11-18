<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    /**
     * Получение списка всех игр.
     */
    public function index(): AnonymousResourceCollection
    {
        $games = Game::query()
            ->orderBy('title')
            ->get();

        return GameResource::collection($games);
    }

    /**
     * Получение информации об одной игре.
     */
    public function show(Game $game): GameResource
    {
        return new GameResource($game);
    }

    /**
     * Создание игры.
     */
    public function store(Request $request): GameResource
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150', 'unique:games,title'],
            'genre' => ['nullable', 'string', 'max:100'],
            'min_age_rating' => ['nullable', 'integer', 'min:0', 'max:255'],
            'icon_url' => ['nullable', 'string', 'max:500'],
        ]);

        $game = Game::create($data);

        return new GameResource($game);
    }

    /**
     * Обновление игры.
     */
    public function update(Request $request, Game $game): GameResource
    {
        $data = $request->validate([
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:150',
                Rule::unique('games', 'title')->ignore($game->id),
            ],
            'genre' => ['sometimes', 'nullable', 'string', 'max:100'],
            'min_age_rating' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:255'],
            'icon_url' => ['sometimes', 'nullable', 'string', 'max:500'],
        ]);

        $game->update($data);

        return new GameResource($game->fresh());
    }

    /**
     * Удаление игры.
     */
    public function destroy(Game $game)
    {
        $game->delete();

        return response()->json([
            'message' => 'Игра успешно удалена.',
        ]);
    }
}
