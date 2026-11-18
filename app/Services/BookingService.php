<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Booking;
use App\Models\Computer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BookingService
{
    public function createBooking(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            $start = Carbon::parse($data['start_time']);
            $now = Carbon::now();

            if ($start->lt($now->copy()->addHour())) {
                abort(422, 'Бронирование необходимо оформлять минимум за час до начала.');
            }

            if (!in_array($start->minute, [0, 15, 30, 45], true) || $start->second !== 0) {
                abort(422, 'Время начала должно быть указано с шагом 15 минут.');
            }

            $package = $data['package'];

            $packages = [
                '1_hour' => ['hours' => 1],
                '3_hours' => ['hours' => 3],
                '5_hours' => ['hours' => 5],
                'night' => ['hours' => 10],
            ];

            if (!isset($packages[$package])) {
                abort(422, 'Неизвестный пакет бронирования.');
            }

            $hour = (int) $start->format('G');

            if ($package === 'night') {
                if (!in_array($hour, [0, 22, 23], true)) {
                    abort(422, 'Ночной тариф можно начать только в 22:00, 23:00 или 00:00.');
                }
            } elseif ($hour < 8 || $hour > 23) {
                abort(422, 'Обычное бронирование доступно с 08:00 до 00:00.');
            }

            $end = $start->copy()->addHours($packages[$package]['hours']);

            // Дополнительные 15 минут до и после бронирования.
            $checkStart = $start->copy()->subMinutes(15);
            $checkEnd = $end->copy()->addMinutes(15);

            if (!empty($data['room_id'])) {
                $room = Room::query()
                    ->with('zone')
                    ->lockForUpdate()
                    ->findOrFail($data['room_id']);

                if ($room->type !== 'bootcamp') {
                    abort(422, 'Целиком можно бронировать только комнату Bootcamp.');
                }

                if (!$room->is_active) {
                    abort(409, 'Эта комната сейчас недоступна.');
                }

                $computers = Computer::query()
                    ->where('room_id', $room->id)
                    ->lockForUpdate()
                    ->get();

                if ($computers->count() !== (int) $room->capacity) {
                    abort(409, 'В комнате не все компьютеры доступны для бронирования.');
                }

                if ($computers->contains(fn ($computer) => $computer->status !== 'available')) {
                    abort(409, 'Один или несколько компьютеров комнаты недоступны.');
                }

                $computerIds = $computers->pluck('id');

                $hasOverlap = Booking::query()
                    ->whereIn('status', ['pending', 'confirmed', 'active'])
                    ->where('start_time', '<', $checkEnd)
                    ->where('end_time', '>', $checkStart)
                    ->where(function ($query) use ($room, $computerIds) {
                        $query->where('room_id', $room->id)
                            ->orWhereIn('computer_id', $computerIds);
                    })
                    ->exists();

                if ($hasOverlap) {
                    abort(409, 'Комната уже забронирована на это время.');
                }

                $totalPrice = (float) $room->zone->{'price_' . $package};
                $bookingType = 'room';
                $computerId = null;
                $roomId = $room->id;
            } else {
                $computer = Computer::query()
                    ->with(['zone', 'room'])
                    ->lockForUpdate()
                    ->findOrFail($data['computer_id']);

                if ($computer->status !== 'available') {
                    abort(409, 'Компьютер сейчас недоступен.');
                }

                if ($computer->room?->type === 'bootcamp') {
                    abort(422, 'Компьютеры Bootcamp бронируются только всей комнатой.');
                }

                $query = Booking::query()
                    ->whereIn('status', ['pending', 'confirmed', 'active'])
                    ->where('start_time', '<', $checkEnd)
                    ->where('end_time', '>', $checkStart)
                    ->where(function ($query) use ($computer) {
                        $query->where('computer_id', $computer->id);

                        if ($computer->room_id) {
                            $query->orWhere(function ($roomQuery) use ($computer) {
                                $roomQuery->where('room_id', $computer->room_id)
                                    ->where('booking_type', 'room');
                            });
                        }
                    });

                if ($query->exists()) {
                    abort(409, 'Компьютер уже забронирован на это время.');
                }

                $totalPrice = (float) $computer->zone->{'price_' . $package};
                $bookingType = 'computer';
                $computerId = $computer->id;
                $roomId = null;
            }

            // Создаём счёт, если он отсутствует.
            $user->newQuery()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            Account::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 0,
                    'tier' => 'standard',
                    'status' => 'active',
                ]
            );

            $account = Account::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $account->status === 'active',
                409,
                'Игровой счёт заблокирован.'
            );

            if ((float) $account->balance < $totalPrice) {
                abort(409, 'Недостаточно средств на игровом балансе.');
            }

            $account->decrement('balance', $totalPrice);

            Payment::create([
                'user_id' => $user->id,
                'amount' => $totalPrice,
                'type' => 'booking_payment',
                'payment_method' => 'balance',
                'status' => 'success',
            ]);

            return Booking::create([
                'user_id' => $user->id,
                'computer_id' => $computerId,
                'room_id' => $roomId,
                'booking_type' => $bookingType,
                'start_time' => $start,
                'end_time' => $end,
                'total_price' => $totalPrice,
                'status' => 'active',
            ]);
        }, 3);
    }

    public function cancelBooking(User $user, Booking $booking): Booking
    {
        return DB::transaction(function () use ($user, $booking) {
            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($booking->id);

            abort_unless(
                $booking->user_id === $user->id,
                403,
                'Нет доступа к этому бронированию.'
            );

            abort_unless(
                in_array($booking->status, ['pending', 'active'], true),
                409,
                'Это бронирование нельзя отменить.'
            );

            $refundPercent = Carbon::now()->lte(
                $booking->start_time->copy()->subHour()
            ) ? 1.0 : 0.5;

            $refundAmount = round(
                (float) $booking->total_price * $refundPercent,
                2
            );

            // Создаём счёт, если он отсутствует.
            $user->newQuery()
                ->whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            Account::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'balance' => 0,
                    'tier' => 'standard',
                    'status' => 'active',
                ]
            );

            $account = Account::query()
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $account->status === 'active',
                409,
                'Игровой счёт заблокирован.'
            );

            $account->increment('balance', $refundAmount);

            Payment::create([
                'user_id' => $user->id,
                'amount' => $refundAmount,
                'type' => 'booking_refund',
                'payment_method' => 'balance',
                'status' => 'success',
            ]);

            $booking->update(['status' => 'cancelled']);
            $booking->setAttribute('refund_amount', $refundAmount);

            return $booking;
        }, 3);
    }
}
