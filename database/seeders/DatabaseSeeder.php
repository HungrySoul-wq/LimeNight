<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use App\Models\Zone;
use App\Models\Computer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {

            // Создаём аккаунты для существующих пользователей
            User::query()->each(function (User $user) {
                Account::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'balance' => 0,
                        'tier' => 'standard',
                        'status' => 'active',
                    ]
                );
            });

            // Общий зал
            $general = Zone::updateOrCreate(
                ['name' => 'Общий зал'],
                [
                    'hourly_rate' => 500,
                    'price_1_hour' => 500,
                    'price_3_hours' => 1300,
                    'price_5_hours' => 2000,
                    'price_night' => 3500,
                    'description' => 'Основной зал, 3 секции по 16 компьютеров.',
                ]
            );

            // VIP
            $vip = Zone::updateOrCreate(
                ['name' => 'VIP'],
                [
                    'hourly_rate' => 750,
                    'price_1_hour' => 750,
                    'price_3_hours' => 1950,
                    'price_5_hours' => 3000,
                    'price_night' => 5250,
                    'description' => 'VIP-зона на 10 компьютеров.',
                ]
            );

            // Bootcamp
            $bootcamp = Zone::updateOrCreate(
                ['name' => 'Bootcamp'],
                [
                    'hourly_rate' => 2000,
                    'price_1_hour' => 2000,
                    'price_3_hours' => 5000,
                    'price_5_hours' => 7500,
                    'price_night' => 12500,
                    'description' => 'Групповая зона на 5 человек. Цена указана за всю компанию.',
                ]
            );

            // Количество компьютеров в каждой зоне
            $computerCounts = [
                $general->id => 48,
                $vip->id => 10,
                $bootcamp->id => 5,
            ];

            foreach ($computerCounts as $zoneId => $count) {
                for ($number = 1; $number <= $count; $number++) {
                    Computer::firstOrCreate(
                        [
                            'zone_id' => $zoneId,
                            'pc_number' => $number,
                        ],
                        [
                            'ip_address' => null,
                            'status' => 'available',
                            'specs' => null,
                        ]
                    );
                }
            }
        });

        $this->call(GameSeeder::class);
        $this->call(ComputerGameSeeder::class);
    }
}
