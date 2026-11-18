<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    /**
     * Список комнат.
     */
    public function index()
    {
        return response()->json([
            'data' => Room::with(['zone', 'computers'])
                ->withCount(['computers', 'bookings'])
                ->latest()
                ->paginate(15),
        ]);
    }

    /**
     * Создание комнаты.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone_id' => [
                'required',
                'integer',
                'exists:zones,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'string',
                'max:100',
                Rule::in(['standard', 'vip', 'bootcamp']),
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $room = Room::create($validated);

        return response()->json([
            'message' => 'Комната успешно создана.',
            'data' => $room->load(['zone', 'computers']),
        ], 201);
    }

    /**
     * Информация о комнате.
     */
    public function show(Room $room)
    {
        return response()->json([
            'data' => $room->load([
                'zone',
                'computers.games',
                'bookings.user',
            ]),
        ]);
    }

    /**
     * Изменение комнаты.
     */
    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'zone_id' => [
                'sometimes',
                'required',
                'integer',
                'exists:zones,id',
            ],

            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::in(['standard', 'vip', 'bootcamp']),
            ],

            'capacity' => [
                'sometimes',
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $room = DB::transaction(function () use ($validated, $room) {
            $room = Room::query()
                ->lockForUpdate()
                ->findOrFail($room->id);

            $computerCount = $room->computers()->count();

            $newCapacity = $validated['capacity'] ?? $room->capacity;

            abort_if(
                $newCapacity < $computerCount,
                422,
                'Вместимость комнаты не может быть меньше количества компьютеров.'
            );

            $zoneChanged = isset($validated['zone_id'])
                && (int) $validated['zone_id'] !== (int) $room->zone_id;

            $structureChanged =
                $zoneChanged
                || (isset($validated['type']) && $validated['type'] !== $room->type)
                || (isset($validated['capacity']) && (int) $validated['capacity'] !== (int) $room->capacity);

            if ($structureChanged) {
                $hasActiveBookings = $room->bookings()
                    ->whereIn('status', ['pending', 'confirmed', 'active'])
                    ->where('end_time', '>=', now())
                    ->exists();

                abort_if(
                    $hasActiveBookings,
                    409,
                    'Нельзя изменять параметры комнаты с действующими бронированиями.'
                );
            }

            $room->update($validated);

            return $room;
        });

        return response()->json([
            'message' => 'Комната успешно обновлена.',
            'data' => $room->fresh()->load(['zone', 'computers']),
        ]);
    }

    /**
     * Удаление комнаты.
     */
    public function destroy(Room $room)
    {
        $hasComputers = $room->computers()->exists();

        $hasBookings = $room->bookings()->exists();

        abort_if(
            $hasComputers || $hasBookings,
            409,
            'Нельзя удалить комнату, пока с ней связаны компьютеры или бронирования.'
        );

        $room->delete();

        return response()->json([
            'message' => 'Комната успешно удалена.',
        ]);
    }
}
