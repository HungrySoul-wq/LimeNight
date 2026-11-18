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
            $computer = Computer::with('zone')
                ->lockForUpdate()
                ->findOrFail($data['computer_id']);

            if ($computer->status !== 'available') {
                abort(409, 'Компьютер сейчас недоступен.');
            }

            $start = Carbon::parse($data['start_time']);
            $now = Carbon::now();

            if ($start->lt($now->copy()->addHour())) {
                abort(422, 'Бронирование необходимо оформлять минимум за час до начала.');
            }

            if ($start->minute !== 0 || $start->second !== 0) {
                abort(422, 'Время начала должно быть указано ровно на час.');
            }

            $package = $data['package'];

            $packages = [
                '1_hour' => [
                    'hours' => 1,
                    'price' => $computer->zone->price_1_hour,
                ],
                '3_hours' => [
                    'hours' => 3,
                    'price' => $computer->zone->price_3_hours,
                ],
                '5_hours' => [
                    'hours' => 5,
                    'price' => $computer->zone->price_5_hours,
                ],
                'night' => [
                    'hours' => 10,
                    'price' => $computer->zone->price_night,
                ],
            ];

            if (!isset($packages[$package])) {
                abort(422, 'Неизвестный пакет бронирования.');
            }

            $selected = $packages[$package];
            $hour = (int) $start->format('G');

            if ($hour < 8 || $hour > 23) {
                abort(422, 'Бронирование доступно с 08:00 до 00:00.');
            }

            if (
                $package === 'night'
                && !in_array($hour, [0, 22, 23], true)
            ) {
                abort(422, 'Ночной пакет можно начать только в 22:00, 23:00 или 00:00.');
            }

            $end = $start->copy()->addHours($selected['hours']);
            $totalPrice = (float) $selected['price'];

            $hasOverlap = Booking::query()
                ->where('computer_id', $computer->id)
                ->whereIn('status', ['pending', 'active'])
                ->where('start_time', '<', $end)
                ->where('end_time', '>', $start)
                ->exists();

            if ($hasOverlap) {
                abort(409, 'Компьютер уже забронирован на это время.');
            }

            $account = Account::where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

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
                'computer_id' => $computer->id,
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

            $account = Account::where('user_id', $user->id)
                ->lockForUpdate()
                ->firstOrFail();

            $account->increment('balance', $refundAmount);

            Payment::create([
                'user_id' => $user->id,
                'amount' => $refundAmount,
                'type' => 'booking_refund',
                'payment_method' => 'balance',
                'status' => 'success',
            ]);

            $booking->update([
                'status' => 'cancelled',
            ]);

            $booking->setAttribute('refund_amount', $refundAmount);

            return $booking;
        }, 3);
    }
}
