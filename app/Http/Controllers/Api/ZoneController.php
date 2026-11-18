<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ZoneResource;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class ZoneController extends Controller
{
    /**
     * Получение списка всех зон.
     */
    public function index(): AnonymousResourceCollection
    {
        $zones = Zone::query()
            ->withCount('computers')
            ->orderBy('id')
            ->get();

        return ZoneResource::collection($zones);
    }

    /**
     * Получение информации об одной зоне.
     */
    public function show(Zone $zone): ZoneResource
    {
        $zone->loadCount('computers');

        return new ZoneResource($zone);
    }

    /**
     * Создание зоны.
     */
    public function store(Request $request): ZoneResource
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:zones,name'],
            'hourly_rate' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_1_hour' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_3_hours' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_5_hours' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_night' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'description' => ['nullable', 'string'],
        ]);

        $zone = Zone::create($data);

        return new ZoneResource($zone);
    }

    /**
     * Обновление зоны.
     */
    public function update(Request $request, Zone $zone): ZoneResource
    {
        $data = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('zones', 'name')->ignore($zone->id),
            ],
            'hourly_rate' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_1_hour' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_3_hours' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_5_hours' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'price_night' => ['sometimes', 'required', 'numeric', 'min:0', 'max:99999999.99'],
            'description' => ['sometimes', 'nullable', 'string'],
        ]);

        $zone->update($data);

        return new ZoneResource($zone->fresh());
    }

    /**
     * Удаление зоны.
     */
    public function destroy(Zone $zone)
    {
        abort_if(
            $zone->computers()->exists(),
            409,
            'Нельзя удалить зону, в которой есть компьютеры.'
        );

        $zone->delete();

        return response()->json([
            'message' => 'Зона успешно удалена.',
        ]);
    }
}
