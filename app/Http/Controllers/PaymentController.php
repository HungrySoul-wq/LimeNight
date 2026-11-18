<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Показывает страницу демонстрационной оплаты.
     */
    public function show(Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        if ($booking->status !== 'pending') {
            return redirect()
                ->route('profile')
                ->with('error', 'Эта бронь уже не ожидает оплаты.');
        }

        $payment = $booking->payments()
            ->where('payment_method', 'card')
            ->where('status', 'pending')
            ->latest()
            ->first();

        if (!$payment) {
            return redirect()
                ->route('profile')
                ->with('error', 'Ожидающий платёж не найден.');
        }

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

        $booking->load(['computer.room', 'room']);

        return view('client.payment', compact(
            'booking',
            'payment'
        ));
    }

    /**
     * Обрабатывает успешную демонстрационную оплату.
     */
    public function demoPay(Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === auth()->id(), 403);

        $result = DB::transaction(function () use ($booking) {

            $lockedBooking = Booking::whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBooking->status !== 'pending') {
                return 'already_processed';
            }

            if (
                !$lockedBooking->expires_at ||
                $lockedBooking->expires_at->isPast()
            ) {
                $lockedBooking->update([
                    'status' => 'cancelled',
                ]);

                Payment::where('booking_id', $lockedBooking->id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'expired',
                    ]);

                return 'expired';
            }

            $payment = Payment::where('booking_id', $lockedBooking->id)
                ->where('user_id', auth()->id())
                ->where('payment_method', 'card')
                ->where('status', 'pending')
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                return 'payment_not_found';
            }

            $payment->update([
                'status' => 'paid',
            ]);

            $lockedBooking->update([
                'status' => 'confirmed',
            ]);

            return 'success';
        });

        if ($result === 'success') {
            return redirect()
                ->route('booking.success', $booking);
        }

        if ($result === 'expired') {
            return redirect()
                ->route('profile')
                ->with(
                    'error',
                    'Время оплаты истекло. Бронирование отменено.'
                );
        }

        if ($result === 'already_processed') {
            return redirect()
                ->route('profile')
                ->with(
                    'error',
                    'Это бронирование уже обработано.'
                );
        }

        return redirect()
            ->route('profile')
            ->with(
                'error',
                'Не удалось найти ожидающий платёж по этой брони.'
            );
    }
}
