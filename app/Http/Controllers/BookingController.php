<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Computer;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Рассчитывает продолжительность и цену выбранного тарифа.
     */
    private function getTariff(Zone $zone, string $duration): array
    {
        return match ($duration) {
            '1' => [1, $zone->price_1_hour],
            '3' => [3, $zone->price_3_hours],
            '5' => [5, $zone->price_5_hours],
            'night' => [8, $zone->price_night],
            default => throw ValidationException::withMessages([
                'duration' => 'Выбран неправильный тариф.',
            ]),
        };
    }

    /**
     * Проверяет разрешённое время начала ночного тарифа.
     */
    private function validateNightStart(Carbon $start): void
    {
        if ($start->hour < 22) {
            throw ValidationException::withMessages([
                'time' => 'Ночной тариф доступен только с 22:00 до 23:59.',
            ]);
        }
    }

    /**
     * Возвращает ближайшее время, округлённое вверх до 15 минут.
     */
    private function getNextBookingStart(): Carbon
    {
        $now = Carbon::now(config('app.timezone'));

        $minutesToAdd = (15 - ($now->minute % 15)) % 15;

        if (
            $minutesToAdd === 0 &&
            $now->format('s.u') !== '00.000000'
        ) {
            $minutesToAdd = 15;
        }

        return $now->copy()
            ->setSecond(0)
            ->setMicrosecond(0)
            ->addMinutes($minutesToAdd);
    }

    /**
     * Отменяет просроченные неоплаченные брони пользователя.
     */
    private function expireUserBookings(int $userId): void
    {
        $expiredBookings = Booking::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredBookings as $booking) {
            $booking->update([
                'status' => 'cancelled',
            ]);

            $booking->payments()
                ->where('status', 'pending')
                ->update([
                    'status' => 'expired',
                ]);
        }
    }

    /**
     * Показывает страницу выбора компьютера или комнаты.
     */
    public function create(Request $request): View
    {
        $zone = Zone::with(['rooms.computers'])
            ->findOrFail($request->query('zone_id'));

        $date = $request->query('date');
        $time = $request->query('time');
        $duration = $request->query('duration');

        if ($request->boolean('now') && (!$date || !$time)) {
            $currentStart = $this->getNextBookingStart();

            $date = $currentStart->format('Y-m-d');
            $time = $currentStart->format('H:i');
            $duration = $duration ?: '1';
        }

        $startTime = null;
        $endTime = null;
        $price = null;

        $bookedComputerIds = [];
        $bookedRoomIds = [];

        $bookedComputerEndTimes = [];
        $bookedRoomEndTimes = [];

        if ($date && $time && $duration) {

            $request->merge([
                'date' => $date,
                'time' => $time,
                'duration' => $duration,
            ]);

            $request->validate([
                'date' => ['required', 'date', 'after_or_equal:today'],
                'time' => ['required', 'date_format:H:i'],
                'duration' => ['required', 'in:1,3,5,night'],
            ]);

            $start = Carbon::createFromFormat(
                'Y-m-d H:i',
                $date . ' ' . $time,
                config('app.timezone')
            );

            if ($start->isPast()) {
                throw ValidationException::withMessages([
                    'time' => 'Выбери время, которое ещё не наступило.',
                ]);
            }

            if ($duration === 'night') {
                $this->validateNightStart($start);
            }

            [$hours, $price] = $this->getTariff($zone, $duration);

            $end = $start->copy()->addHours($hours);

            $startTime = $start->toDateTimeString();
            $endTime = $end->toDateTimeString();

            $computerBookings = Booking::query()
                ->select('computer_id')
                ->selectRaw('MAX(end_time) as available_at')
                ->whereNotNull('computer_id')
                ->where(function ($query) {
                    $query->where('status', 'confirmed')
                        ->orWhere(function ($pending) {
                            $pending->where('status', 'pending')
                                ->where(function ($expiry) {
                                    $expiry->whereNull('expires_at')
                                        ->orWhere('expires_at', '>', now());
                                });
                        });
                })
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->groupBy('computer_id')
                ->get();

            foreach ($computerBookings as $booking) {
                $bookedComputerEndTimes[$booking->computer_id] = $booking->available_at;
            }

            $bookedComputerIds = array_keys($bookedComputerEndTimes);

            $roomBookings = Booking::query()
                ->select('room_id')
                ->selectRaw('MAX(end_time) as available_at')
                ->where('booking_type', 'room')
                ->whereNotNull('room_id')
                ->where(function ($query) {
                    $query->where('status', 'confirmed')
                        ->orWhere(function ($pending) {
                            $pending->where('status', 'pending')
                                ->where(function ($expiry) {
                                    $expiry->whereNull('expires_at')
                                        ->orWhere('expires_at', '>', now());
                                });
                        });
                })
                ->where('start_time', '<', $endTime)
                ->where('end_time', '>', $startTime)
                ->groupBy('room_id')
                ->get();

            foreach ($roomBookings as $booking) {
                $bookedRoomEndTimes[$booking->room_id] = $booking->available_at;
            }

            $bookedRoomIds = array_keys($bookedRoomEndTimes);
        }

        return view('client.booking', compact(
            'zone',
            'date',
            'time',
            'duration',
            'startTime',
            'endTime',
            'price',
            'bookedComputerIds',
            'bookedRoomIds',
            'bookedComputerEndTimes',
            'bookedRoomEndTimes'
        ));
    }

    /**
     * Создаёт бронирование и платёж.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'zone_id' => ['required', 'exists:zones,id'],
            'booking_type' => ['required', 'in:computer,room'],

            'computer_id' => [
                'nullable',
                'required_if:booking_type,computer',
                'exists:computers,id',
            ],

            'room_id' => [
                'nullable',
                'required_if:booking_type,room',
                'exists:rooms,id',
            ],

            'date' => ['required', 'date', 'after_or_equal:today'],
            'time' => ['required', 'date_format:H:i'],
            'duration' => ['required', 'in:1,3,5,night'],

            'payment_method' => ['required', 'in:card,onsite'],
        ]);

        $zone = Zone::findOrFail($data['zone_id']);

        $start = Carbon::createFromFormat(
            'Y-m-d H:i',
            $data['date'] . ' ' . $data['time'],
            config('app.timezone')
        );

        if ($start->isPast()) {
            throw ValidationException::withMessages([
                'time' => 'Нельзя оформить бронь на прошедшее время.',
            ]);
        }

        if ($data['duration'] === 'night') {
            $this->validateNightStart($start);
        }

        [$hours, $price] = $this->getTariff(
            $zone,
            $data['duration']
        );

        $end = $start->copy()->addHours($hours);

        $booking = DB::transaction(function () use (
            $data,
            $zone,
            $start,
            $end,
            $price
        ) {
            User::whereKey(auth()->id())
                ->lockForUpdate()
                ->firstOrFail();

            $this->expireUserBookings(auth()->id());

            $activeBooking = Booking::where('user_id', auth()->id())
                ->where(function ($query) {
                    $query->where(function ($confirmed) {
                        $confirmed->where('status', 'confirmed')
                            ->where('end_time', '>', now());
                    })->orWhere(function ($pending) {
                        $pending->where('status', 'pending')
                            ->where('expires_at', '>', now());
                    });
                })
                ->exists();

            if ($activeBooking) {
                throw ValidationException::withMessages([
                    'booking' => 'У тебя уже есть активное бронирование. Сначала заверши или отмени его.',
                ]);
            }

            if ($data['booking_type'] === 'computer') {

                $computer = Computer::where('id', $data['computer_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                if (
                    $computer->zone_id != $zone->id ||
                    $computer->status !== 'available' ||
                    !$computer->room ||
                    !$computer->room->is_active
                ) {
                    throw ValidationException::withMessages([
                        'computer_id' => 'Этот компьютер сейчас недоступен.',
                    ]);
                }

                $conflict = Booking::where(function ($query) {
                        $query->where('status', 'confirmed')
                            ->orWhere(function ($pending) {
                                $pending->where('status', 'pending')
                                    ->where(function ($expiry) {
                                        $expiry->whereNull('expires_at')
                                            ->orWhere('expires_at', '>', now());
                                    });
                            });
                    })
                    ->where(function ($query) use ($computer) {
                        $query->where('computer_id', $computer->id)
                            ->orWhere(function ($roomQuery) use ($computer) {
                                $roomQuery->where('booking_type', 'room')
                                    ->where('room_id', $computer->room_id);
                            });
                    })
                    ->where('start_time', '<', $end)
                    ->where('end_time', '>', $start)
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'computer_id' => 'Этот компьютер уже забронирован на выбранное время.',
                    ]);
                }

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'computer_id' => $computer->id,
                    'room_id' => null,
                    'booking_type' => 'computer',
                    'start_time' => $start,
                    'end_time' => $end,
                    'total_price' => $price,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(15),
                ]);

            } else {

                $room = Room::where('id', $data['room_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                $isBootcamp = $room->type === 'bootcamp'
                    || str_contains(mb_strtolower($zone->name), 'bootcamp');

                if (
                    $room->zone_id != $zone->id ||
                    !$room->is_active ||
                    !$isBootcamp
                ) {
                    throw ValidationException::withMessages([
                        'room_id' => 'Эту комнату нельзя забронировать как Bootcamp.',
                    ]);
                }

                $conflict = Booking::where(function ($query) {
                        $query->where('status', 'confirmed')
                            ->orWhere(function ($pending) {
                                $pending->where('status', 'pending')
                                    ->where(function ($expiry) {
                                        $expiry->whereNull('expires_at')
                                            ->orWhere('expires_at', '>', now());
                                    });
                            });
                    })
                    ->where(function ($query) use ($room) {
                        $query->where(function ($roomQuery) use ($room) {
                            $roomQuery->where('booking_type', 'room')
                                ->where('room_id', $room->id);
                        })->orWhereIn(
                            'computer_id',
                            $room->computers()->select('id')
                        );
                    })
                    ->where('start_time', '<', $end)
                    ->where('end_time', '>', $start)
                    ->exists();

                if ($conflict) {
                    throw ValidationException::withMessages([
                        'room_id' => 'Комната Bootcamp уже занята на выбранное время.',
                    ]);
                }

                $booking = Booking::create([
                    'user_id' => auth()->id(),
                    'computer_id' => null,
                    'room_id' => $room->id,
                    'booking_type' => 'room',
                    'start_time' => $start,
                    'end_time' => $end,
                    'total_price' => $price,
                    'status' => 'pending',
                    'expires_at' => now()->addMinutes(15),
                ]);
            }

            Payment::create([
                'user_id' => auth()->id(),
                'booking_id' => $booking->id,
                'amount' => $price,
                'type' => 'booking',
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
            ]);

            return $booking;
        });

        if ($data['payment_method'] === 'card') {
            return redirect()->route('payment.show', $booking);
        }

        return redirect()->route('booking.success', $booking);
    }

    /**
     * Показывает страницу успешного создания бронирования.
     */
    public function success(Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $booking->load([
            'computer.zone',
            'computer.room',
            'room.zone',
            'payments',
        ]);

        if ($booking->status === 'pending') {
            if (!$booking->expires_at || $booking->expires_at->isPast()) {
                DB::transaction(function () use ($booking) {
                    $lockedBooking = Booking::whereKey($booking->id)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if (
                        $lockedBooking->status === 'pending' &&
                        (
                            !$lockedBooking->expires_at ||
                            $lockedBooking->expires_at->isPast()
                        )
                    ) {
                        $lockedBooking->update([
                            'status' => 'cancelled',
                        ]);

                        Payment::where('booking_id', $lockedBooking->id)
                            ->where('status', 'pending')
                            ->update([
                                'status' => 'expired',
                            ]);
                    }
                });

                return redirect()
                    ->route('profile')
                    ->with('error', 'Время оплаты истекло. Бронирование отменено.');
            }

            $paymentMethod = $booking->payments
                ->where('status', 'pending')
                ->first()?->payment_method;

            if ($paymentMethod !== 'onsite') {
                return redirect()->route('payment.show', $booking);
            }
        }

        if ($booking->status !== 'pending' && $booking->status !== 'confirmed') {
            return redirect()
                ->route('profile')
                ->with('error', 'Это бронирование больше не активно.');
        }

        return view('client.booking-success', compact('booking'));
    }
}
