<?php

namespace Database\Seeders;

use App\Models\Zone;
use App\Models\Room;
use App\Models\Computer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LimeNightSeeder extends Seeder
{
    public function run(): void
    {
        // Выполняем заполнение в одной транзакции.
        // Если возникнет ошибка, изменения не сохранятся частично.
        DB::transaction(function () {

            // ==========================================
            // 1. СОЗДАЁМ ЗОНЫ И ЗАПИСЫВАЕМ ИХ ТАРИФЫ
            // ==========================================

            $zones = [
                [
                    'name' => 'Общий зал',
                    'hourly_rate' => 500,
                    'price_1_hour' => 500,
                    'price_3_hours' => 1300,
                    'price_5_hours' => 2000,
                    'price_night' => 3500,
                    'description' => 'Общий компьютерный зал с тремя отдельными комнатами.',
                ],
                [
                    'name' => 'VIP',
                    'hourly_rate' => 750,
                    'price_1_hour' => 750,
                    'price_3_hours' => 1950,
                    'price_5_hours' => 3000,
                    'price_night' => 5250,
                    'description' => 'Отдельная VIP-комната на 10 компьютеров.',
                ],
                [
                    'name' => 'Bootcamp',
                    'hourly_rate' => 2000,
                    'price_1_hour' => 2000,
                    'price_3_hours' => 5000,
                    'price_5_hours' => 7500,
                    'price_night' => 12500,
                    'description' => 'Командная комната на 5 компьютеров. Цена указана за всю комнату.',
                ],
            ];

            foreach ($zones as $zoneData) {

                // Ищем зону по названию или создаём новую.
                $zone = Zone::firstOrNew([
                    'name' => $zoneData['name'],
                ]);

                // Записываем тарифы и описание зоны.
                foreach ($zoneData as $field => $value) {
                    $zone->$field = $value;
                }

                $zone->save();

                // ==========================================
                // 2. ОПРЕДЕЛЯЕМ КОМНАТЫ ВНУТРИ КАЖДОЙ ЗОНЫ
                // ==========================================

                $rooms = [];

                if ($zoneData['name'] === 'Общий зал') {
                    $rooms = [
                        ['name' => 'Комната 1', 'type' => 'standard', 'capacity' => 16],
                        ['name' => 'Комната 2', 'type' => 'standard', 'capacity' => 16],
                        ['name' => 'Комната 3', 'type' => 'standard', 'capacity' => 16],
                    ];
                }

                if ($zoneData['name'] === 'VIP') {
                    $rooms = [
                        ['name' => 'VIP-комната', 'type' => 'vip', 'capacity' => 10],
                    ];
                }

                if ($zoneData['name'] === 'Bootcamp') {
                    $rooms = [
                        ['name' => 'Bootcamp-комната', 'type' => 'bootcamp', 'capacity' => 5],
                    ];
                }

                // ==========================================
                // 3. СОЗДАЁМ КОМНАТЫ
                // ==========================================

                foreach ($rooms as $roomData) {

                    // Комната определяется сочетанием зоны и названия.
                    $room = Room::firstOrNew([
                        'zone_id' => $zone->id,
                        'name' => $roomData['name'],
                    ]);

                    $room->zone_id = $zone->id;
                    $room->name = $roomData['name'];
                    $room->type = $roomData['type'];
                    $room->capacity = $roomData['capacity'];
                    $room->is_active = true;

                    $room->save();

                    // ==========================================
                    // 4. СОЗДАЁМ КОМПЬЮТЕРЫ В КАЖДОЙ КОМНАТЕ
                    // ==========================================

                    for ($number = 1; $number <= $roomData['capacity']; $number++) {

                        // Ищем компьютер по комнате и его номеру.
                        $computer = Computer::firstOrNew([
                            'room_id' => $room->id,
                            'pc_number' => $number,
                        ]);

                        $computer->zone_id = $zone->id;
                        $computer->room_id = $room->id;
                        $computer->pc_number = $number;

                        // Для нового компьютера устанавливаем статус
                        // «свободен». Существующий статус не перезаписываем.
                        if (!$computer->exists) {
                            $computer->status = 'available';
                        }

                        $computer->save();
                    }
                }
            }
        });

        $this->command?->info(
            'LimeNight: зоны, комнаты и компьютеры успешно подготовлены.'
        );
    }
}
