<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoomController extends Controller
{
    /**
     * Список всех комнат.
     */
    public function index(): View
    {
        $rooms = Room::with(['zone', 'computers'])
            ->orderBy('zone_id')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.rooms.index', compact('rooms'));
    }

    /**
     * Форма добавления комнаты.
     */
    public function create(): View
    {
        $zones = Zone::orderBy('name')->get();

        return view('admin.rooms.create', compact('zones'));
    }

    /**
     * Сохранение новой комнаты.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rooms', 'name')
                    ->where('zone_id', $request->input('zone_id')),
            ],
            'type' => [
                'required',
                Rule::in(['standard', 'vip', 'bootcamp']),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['required', 'boolean'],
        ]);

        Room::create($validated);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Комната успешно добавлена.');
    }

    /**
     * Форма редактирования комнаты.
     */
    public function edit(Room $room): View
    {
        $zones = Zone::orderBy('name')->get();

        return view('admin.rooms.edit', compact('room', 'zones'));
    }

    /**
     * Обновление комнаты.
     */
    public function update(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('rooms', 'name')
                    ->where('zone_id', $request->input('zone_id'))
                    ->ignore($room->id),
            ],
            'type' => [
                'required',
                Rule::in(['standard', 'vip', 'bootcamp']),
            ],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'is_active' => ['required', 'boolean'],
        ]);

        $room->update($validated);

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Комната успешно обновлена.');
    }

    /**
     * Удаление комнаты.
     */
    public function destroy(Room $room): RedirectResponse
    {
        if (
            $room->computers()->exists() ||
            $room->bookings()->exists()
        ) {
            return redirect()
                ->route('admin.rooms.index')
                ->with('error', 'Нельзя удалить комнату, в которой есть компьютеры или бронирования.');
        }

        $room->delete();

        return redirect()
            ->route('admin.rooms.index')
            ->with('success', 'Комната успешно удалена.');
    }
}
