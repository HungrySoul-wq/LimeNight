<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Показывает личный кабинет и историю бронирований.
     */
    public function index(): View
    {
        $bookings = Booking::with([
            'computer.room',
            'computer.zone',
            'room.zone',
            'payments',
        ])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(5);

        return view('client.profile', compact('bookings'));
    }

    /**
     * Отменяет бронирование пользователем.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        // Проверяем, что бронирование принадлежит текущему пользователю.
        abort_unless($booking->user_id === Auth::id(), 403);

        $result = DB::transaction(function () use ($booking) {

            $lockedBooking = Booking::whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Отменять можно только ожидающие оплаты брони.
            if ($lockedBooking->status !== 'pending') {
                return 'cannot_cancel';
            }

            // Нельзя отменить бронь, время которой уже наступило.
            if (
                !$lockedBooking->start_time ||
                !$lockedBooking->start_time->isFuture()
            ) {
                return 'time_started';
            }

            // Отменяем бронирование.
            $lockedBooking->update([
                'status' => 'cancelled',
            ]);

            // Отменяем связанные ожидающие платежи.
            $lockedBooking->payments()
                ->where('status', 'pending')
                ->update([
                    'status' => 'cancelled',
                ]);

            return 'success';
        });

        if ($result === 'success') {
            return redirect()
                ->route('profile')
                ->with(
                    'success',
                    'Бронирование №' . $booking->id . ' успешно отменено.'
                );
        }

        if ($result === 'time_started') {
            return redirect()
                ->route('profile')
                ->withErrors([
                    'booking' => 'Нельзя отменить бронирование, время начала которого уже наступило.',
                ]);
        }

        return redirect()
            ->route('profile')
            ->withErrors([
                'booking' => 'Это бронирование уже нельзя отменить.',
            ]);
    }
}
