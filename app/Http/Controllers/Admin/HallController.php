<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HallController extends Controller
{
    /**
     * Список игровых зон.
     */
    public function index(): View
    {
        $zones = Zone::with(['rooms.computers'])
            ->latest()
            ->paginate(10);

        return view('admin.halls.index', compact('zones'));
    }

    /**
     * Форма добавления игровой зоны.
     */
    public function create(): View
    {
        return view('admin.halls.create');
    }

    /**
     * Сохранение новой игровой зоны.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'price_1_hour' => ['nullable', 'numeric', 'min:0'],
            'price_3_hours' => ['nullable', 'numeric', 'min:0'],
            'price_5_hours' => ['nullable', 'numeric', 'min:0'],
            'price_night' => ['nullable', 'numeric', 'min:0'],
        ]);

        Zone::create($validated);

        return redirect()
            ->route('admin.halls.index')
            ->with('success', 'Игровая зона успешно добавлена.');
    }

    /**
     * Форма редактирования игровой зоны.
     */
    public function edit(Zone $hall): View
    {
        return view('admin.halls.edit', [
            'zone' => $hall,
        ]);
    }

    /**
     * Обновление игровой зоны.
     */
    public function update(Request $request, Zone $hall): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'hourly_rate' => ['nullable', 'numeric', 'min:0'],
            'price_1_hour' => ['nullable', 'numeric', 'min:0'],
            'price_3_hours' => ['nullable', 'numeric', 'min:0'],
            'price_5_hours' => ['nullable', 'numeric', 'min:0'],
            'price_night' => ['nullable', 'numeric', 'min:0'],
        ]);

        $hall->update($validated);

        return redirect()
            ->route('admin.halls.index')
            ->with('success', 'Игровая зона успешно обновлена.');
    }

    /**
     * Удаление игровой зоны.
     */
    public function destroy(Zone $hall): RedirectResponse
    {
        if ($hall->rooms()->exists() || $hall->computers()->exists()) {
            return redirect()
                ->route('admin.halls.index')
                ->with('error', 'Нельзя удалить зону, в которой есть комнаты или компьютеры.');
        }

        $hall->delete();

        return redirect()
            ->route('admin.halls.index')
            ->with('success', 'Игровая зона удалена.');
    }
}
