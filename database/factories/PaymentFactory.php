<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 100, 5000),
            'type' => fake()->randomElement([
                'deposit',
                'payment',
                'refund',
            ]),
            'payment_method' => fake()->randomElement([
                'cash',
                'card',
                'online',
            ]),
            'status' => 'completed',
        ];
    }
}