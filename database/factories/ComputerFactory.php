<?php

namespace Database\Factories;

use App\Models\Computer;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Computer>
 */
class ComputerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'zone_id' => Zone::factory(),
            'pc_number' => fake()->unique()->numberBetween(1, 63),
            'ip_address' => fake()->optional()->ipv4(),
            'status' => 'available',
            'specs' => [
                'cpu' => 'Test CPU',
                'ram' => '16 GB',
                'gpu' => 'Test GPU',
            ],
        ];
    }
}