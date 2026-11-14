<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Booking;
use App\Models\Computer;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $computer = Computer::with('zone')->findOrFail($data['computer_id']);

            // 1. Проверка статуса оборудования
            if ($computer->status === 'maintenance') {
                abort(409, 'Компьютер находится на техническом обслуживании и недоступен для бронирования.');
            }

            // 2. Проверка пересечения временных интервалов
            $hasOverlap = Booking::where('computer_id', $computer->id)
                ->whereIn('status', ['pending', 'active'])
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->exists();

            if ($hasOverlap) {
                abort(409, 'Компьютер уже забронирован на выбранный временной интервал.');
            }

            // 3. Расчёт стоимости (длительность в часах * тариф зоны)
            $start = Carbon::parse($data['start_time']);
            $end = Carbon::parse($data['end_time']);
            $hours = $start->diffInMinutes($end) / 60;
            $totalPrice = round($hours * $computer->zone->hourly_rate, 2);

            // 4. Проверка и списание баланса аккаунта (с блокировкой строки)
            $account = Account::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ($account->balance < $totalPrice) {
                abort(409, 'Недостаточно средств на игровом балансе для оформления брони.');
            }

            $account->decrement('balance', $totalPrice);

            // 5. Фиксация финансовой транзакции
            Payment::create([
                'user_id' => $user->id,
                'amount' => $totalPrice,
                'type' => 'booking_payment',
                'payment_method' => 'balance',
                'status' => 'success',
            ]);

            // 6. Создание записи бронирования
            return Booking::create([
                'user_id' => $user->id,
                'computer_id' => $computer->id,
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'total_price' => $totalPrice,
                'status' => 'active',
            ]);
        });
    }
}