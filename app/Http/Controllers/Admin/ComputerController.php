<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Computer;
use App\Models\Room;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComputerController extends Controller
{
    /**
     * Список всех компьютеров.
     */
    public function index(): View
    {
        $computers = Computer::with(['zone', 'room'])
            ->orderBy('zone_id')
            ->orderBy('room_id')
            ->orderBy('pc_number')
            ->paginate(20);

        return view('admin.computers.index', compact('computers'));
    }

    /**
     * Форма добавления компьютера.
     */
    public function create(Request $request): View
    {
        $zones = Zone::with('rooms')->orderBy('name')->get();

        $selectedRoom = null;

        if ($request->filled('room_id')) {
            $selectedRoom = Room::with('zone')
                ->find($request->integer('room_id'));

            if ($selectedRoom) {
                $request->merge([
                    'zone_id' => $selectedRoom->zone_id,
                ]);
            }
        }

        return view('admin.computers.create', compact(
            'zones',
            'selectedRoom'
        ));
    }

    /**
     * Сохранение нового компьютера.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],

            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')
                    ->where('zone_id', $request->input('zone_id')),
            ],

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
                'required',
                Rule::in([
                    'available',
                    'occupied',
                    'reserved',
                    'maintenance',
                ]),
            ],

            'specs' => ['nullable', 'string', 'max:5000'],
        ]);

        $specs = trim($validated['specs'] ?? '');

        unset($validated['specs']);

        $validated['specs'] = $specs !== ''
            ? ['description' => $specs]
            : null;

        Computer::create($validated);

        return redirect()
            ->route('admin.computers.index')
            ->with('success', 'Компьютер успешно добавлен.');
    }

    /**
     * Форма редактирования компьютера.
     */
    public function edit(Computer $computer): View
    {
        $zones = Zone::with('rooms')->orderBy('name')->get();

        return view('admin.computers.edit', compact(
            'computer',
            'zones'
        ));
    }

    /**
     * Обновление компьютера.
     */
    public function update(Request $request, Computer $computer): RedirectResponse
    {
        $validated = $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],

            'room_id' => [
                'required',
                Rule::exists('rooms', 'id')
                    ->where('zone_id', $request->input('zone_id')),
            ],

            'pc_number' => [
                'required',
                'integer',
                'min:1',
                'max:65535',
                Rule::unique('computers', 'pc_number')
                    ->where('room_id', $request->input('room_id'))
                    ->ignore($computer->id),
            ],

            'ip_address' => ['nullable', 'ip'],

            'status' => [
                'required',
                Rule::in([
                    'available',
                    'occupied',
                    'reserved',
                    'maintenance',
                ]),
            ],

            'specs' => ['nullable', 'string', 'max:5000'],
        ]);

        $specs = trim($validated['specs'] ?? '');

        unset($validated['specs']);

        $validated['specs'] = $specs !== ''
            ? ['description' => $specs]
            : null;

        $computer->update($validated);

        return redirect()
            ->route('admin.computers.index')
            ->with('success', 'Данные компьютера обновлены.');
    }

    /**
     * Удаление компьютера.
     */
    public function destroy(Computer $computer): RedirectResponse
    {
        if ($computer->bookings()->exists()) {
            return redirect()
                ->route('admin.computers.index')
                ->with('error', 'Нельзя удалить компьютер, у которого есть бронирования.');
        }

        $computer->delete();

        return redirect()
            ->route('admin.computers.index')
            ->with('success', 'Компьютер удалён.');
    }
}
