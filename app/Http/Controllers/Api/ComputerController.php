<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ComputerResource;
use App\Models\Computer;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ComputerController extends Controller
{
    /**
     * Список компьютеров с бронированиями компьютеров и целых комнат.
     */
    public function index(): AnonymousResourceCollection
    {
        $computers = Computer::query()
            ->with([
                'zone',
                'room.bookings' => function ($query) {
                    $query->whereIn('status', ['pending', 'confirmed', 'active'])
                        ->where('end_time', '>=', now())
                        ->orderBy('start_time');
                },
                'games',
                'bookings' => function ($query) {
                    $query->whereIn('status', ['pending', 'confirmed', 'active'])
                        ->where('end_time', '>=', now())
                        ->orderBy('start_time');
                },
            ])
            ->orderBy('zone_id')
            ->orderBy('room_id')
            ->orderBy('pc_number')
            ->get();

        $this->includeRoomBookings($computers);

        return ComputerResource::collection($computers);
    }

    /**
     * Информация об одном компьютере.
     */
    public function show(Computer $computer): ComputerResource
    {
        $computer->load([
            'zone',
            'room.bookings' => function ($query) {
                $query->whereIn('status', ['pending', 'confirmed', 'active'])
                    ->where('end_time', '>=', now())
                    ->orderBy('start_time');
            },
            'games',
            'bookings' => function ($query) {
                $query->whereIn('status', ['pending', 'confirmed', 'active'])
                    ->where('end_time', '>=', now())
                    ->orderBy('start_time');
            },
        ]);

        $this->includeRoomBookings(collect([$computer]));

        return new ComputerResource($computer);
    }

    /**
     * Добавляет бронирования всей комнаты к каждому её компьютеру.
     */
    private function includeRoomBookings($computers): void
    {
        $computers->each(function ($computer) {
            if (!$computer->room) {
                return;
            }

            $computerBookings = $computer->bookings ?? collect();
            $roomBookings = $computer->room->bookings ?? collect();

            $computer->setRelation(
                'bookings',
                $computerBookings
                    ->concat($roomBookings)
                    ->unique('id')
                    ->sortBy('start_time')
                    ->values()
            );
        });
    }

    /**
     * Проверяет, относится ли комната к выбранной игровой зоне.
     */
    private function validateRoomZone(?int $roomId, int $zoneId): void
    {
        if (!$roomId) {
            return;
        }

        abort_unless(
            Room::where('id', $roomId)
                ->where('zone_id', $zoneId)
                ->exists(),
            422,
            'Выбранная комната не относится к этой игровой зоне.'
        );
    }

    /**
     * Проверяет наличие действующих бронирований комнаты.
     */
    private function roomHasActiveBookings(int $roomId): bool
    {
        return Room::findOrFail($roomId)
            ->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'active'])
            ->where('end_time', '>=', now())
            ->exists();
    }

    /**
     * Добавление компьютера.
     */
    public function store(Request $request): ComputerResource
    {
        $data = $request->validate([
            'zone_id' => ['required', 'integer', 'exists:zones,id'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],

            'pc_number' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('computers', 'pc_number')
                    ->where('room_id', $request->input('room_id')),
            ],

            'ip_address' => ['nullable', 'ip'],

            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['available', 'occupied', 'maintenance']),
            ],

            'specs' => ['nullable', 'array'],
            'games' => ['sometimes', 'array'],
            'games.*' => ['integer', 'distinct', 'exists:games,id'],
        ]);

        $this->validateRoomZone(
            $data['room_id'] ?? null,
            (int) $data['zone_id']
        );

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
            $computer->load(['zone', 'room', 'games'])
        );
    }

    /**
     * Изменение компьютера.
     */
    public function update(Request $request, Computer $computer): ComputerResource
    {
        $data = $request->validate([
            'zone_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:zones,id',
            ],

            'room_id' => [
                'sometimes',
                'nullable',
                'integer',
                'exists:rooms,id',
            ],

            'pc_number' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('computers', 'pc_number')
                    ->where(
                        'room_id',
                        $request->input('room_id', $computer->room_id)
                    )
                    ->ignore($computer->id),
            ],

            'ip_address' => ['sometimes', 'nullable', 'ip'],

            'status' => [
                'sometimes',
                'required',
                'string',
                Rule::in(['available', 'occupied', 'maintenance']),
            ],

            'specs' => ['sometimes', 'nullable', 'array'],
            'games' => ['sometimes', 'array'],
            'games.*' => ['integer', 'distinct', 'exists:games,id'],
        ]);

        $zoneId = (int) ($data['zone_id'] ?? $computer->zone_id);

        $roomId = array_key_exists('room_id', $data)
            ? $data['room_id']
            : $computer->room_id;

        $this->validateRoomZone(
            $roomId ? (int) $roomId : null,
            $zoneId
        );

        $computer = DB::transaction(function () use (
            $data,
            $computer,
            $roomId
        ) {
            $computer = Computer::query()
                ->lockForUpdate()
                ->findOrFail($computer->id);

            $roomChanged = (int) $computer->room_id !== (int) $roomId;

            if ($roomChanged) {
                abort_if(
                    $computer->bookings()
                        ->whereIn('status', ['pending', 'confirmed', 'active'])
                        ->where('end_time', '>=', now())
                        ->exists(),
                    409,
                    'Нельзя переместить компьютер с действующим бронированием.'
                );

                if ($computer->room_id) {
                    abort_if(
                        $this->roomHasActiveBookings($computer->room_id),
                        409,
                        'Нельзя изменить состав комнаты с действующим бронированием.'
                    );
                }

                if ($roomId) {
                    abort_if(
                        $this->roomHasActiveBookings((int) $roomId),
                        409,
                        'Нельзя добавить компьютер в комнату с действующим бронированием.'
                    );
                }
            }

            if (array_key_exists('games', $data)) {
                $computer->games()->sync($data['games']);
                unset($data['games']);
            }

            $computer->update($data);

            return $computer;
        });

        return new ComputerResource(
            $computer->fresh()->load(['zone', 'room', 'games'])
        );
    }

    /**
     * Удаление компьютера.
     */
    public function destroy(Computer $computer)
    {
        abort_if(
            $computer->bookings()->exists(),
            409,
            'Нельзя удалить компьютер, у которого есть бронирования.'
        );

        if ($computer->room_id) {
            abort_if(
                $this->roomHasActiveBookings($computer->room_id),
                409,
                'Нельзя удалить компьютер из комнаты с действующим бронированием.'
            );
        }

        $computer->delete();

        return response()->json([
            'message' => 'Компьютер успешно удалён.',
        ]);
    }
}
