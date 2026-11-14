<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Computer;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('+1 day', '+7 days');
        $endTime = (clone $startTime)->modify('+1 hour');

        return [
            'user_id' => User::factory(),
            'computer_id' => Computer::factory(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => 100,
            'status' => 'pending',
        ];
    }
}