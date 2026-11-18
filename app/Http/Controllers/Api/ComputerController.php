<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComputerResource;
use App\Models\Computer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ComputerController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $computers = Computer::query()
            ->with(['zone', 'games'])
            ->orderBy('zone_id')
            ->orderBy('pc_number')
            ->get();

        return ComputerResource::collection($computers);
    }

    public function show(Computer $computer): ComputerResource
    {
        $computer->load(['zone', 'games']);

        return new ComputerResource($computer);
    }

    public function store(Request $request): ComputerResource
    {
        $data = $request->validate([
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
            'pc_number' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('computers', 'pc_number')
                    ->where('zone_id', $request->input('zone_id')),
            ],
            'ip_address' => ['nullable', 'ip'],
            'status' => ['sometimes', 'required', 'string', Rule::in([
                'available',
                'occupied',
                'maintenance',
            ])],
            'specs' => ['nullable', 'array'],
            'games' => ['sometimes', 'array'],
            'games.*' => ['integer', 'distinct', 'exists:games,id'],
        ]);

        $hasGames = array_key_exists('games', $data);
        $games = $data['games'] ?? [];
        unset($data['games']);

        $computer = DB::transaction(function () use ($data, $games, $hasGames) {
            $computer = Computer::create($data);

            if ($hasGames) {
                $computer->games()->sync($games);
            }

            return $computer;
        });

        return new ComputerResource(
            $computer->load(['zone', 'games'])
        );
    }

    public function update(Request $request, Computer $computer): ComputerResource
    {
        $data = $request->validate([
            'zone_id' => ['sometimes', 'required', 'integer', 'exists:zones,id'],
            'pc_number' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('computers', 'pc_number')
                    ->where('zone_id', $request->input('zone_id', $computer->zone_id))
                    ->ignore($computer->id),
            ],
            'ip_address' => ['sometimes', 'nullable', 'ip'],
            'status' => ['sometimes', 'required', 'string', Rule::in([
                'available',
                'occupied',
                'maintenance',
            ])],
            'specs' => ['sometimes', 'nullable', 'array'],
            'games' => ['sometimes', 'array'],
            'games.*' => ['integer', 'distinct', 'exists:games,id'],
        ]);

        DB::transaction(function () use ($data, $computer) {
            if (array_key_exists('games', $data)) {
                $computer->games()->sync($data['games']);
                unset($data['games']);
            }

            $computer->update($data);
        });

        return new ComputerResource(
            $computer->fresh()->load(['zone', 'games'])
        );
    }

    public function destroy(Computer $computer)
    {
        abort_if(
            $computer->bookings()->exists(),
            409,
            'Нельзя удалить компьютер, у которого есть бронирования.'
        );

        $computer->delete();

        return response()->json([
            'message' => 'Компьютер успешно удалён.',
        ]);
    }
}
