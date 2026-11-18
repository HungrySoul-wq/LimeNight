<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceController extends Controller
{
    public function index(): View
    {
        $zones = Zone::orderBy('id')->get();

        return view('admin.prices.index', compact('zones'));
    }

    public function edit(Zone $zone): View
    {
        return view('admin.prices.edit', compact('zone'));
    }

    public function update(Request $request, Zone $zone): RedirectResponse
    {
        $validated = $request->validate([
            'price_1_hour' => ['required', 'numeric', 'min:0'],
            'price_3_hours' => ['required', 'numeric', 'min:0'],
            'price_5_hours' => ['required', 'numeric', 'min:0'],
            'price_night' => ['required', 'numeric', 'min:0'],
        ], [
            'price_1_hour.required' => 'Укажите стоимость одного часа.',
            'price_3_hours.required' => 'Укажите стоимость трёх часов.',
            'price_5_hours.required' => 'Укажите стоимость пяти часов.',
            'price_night.required' => 'Укажите стоимость ночного тарифа.',
            '*.numeric' => 'Цена должна быть числом.',
            '*.min' => 'Цена не может быть отрицательной.',
        ]);

        $validated['hourly_rate'] = $validated['price_1_hour'];

        $zone->update($validated);

        return redirect()
            ->route('admin.prices.index')
            ->with('success', 'Тарифы зоны «' . $zone->name . '» успешно обновлены.');
    }
}
