<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Список всех бронирований.
     */
    public function index(): View
    {
        $bookings = Booking::with([
            'user',
            'computer.zone',
            'computer.room',
            'room.zone',
            'payments',
        ])
            ->latest()
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Подтверждение оплаты администратором на месте.
     */
    public function confirmPayment(Booking $booking): RedirectResponse
    {
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
                ->where('payment_method', 'onsite')
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
            return redirect()->route('admin.bookings.index')
                ->with('success', 'Оплата получена. Бронирование №' . $booking->id . ' подтверждено.');
        }

        if ($result === 'expired') {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Срок оплаты бронирования №' . $booking->id . ' истёк. Бронь отменена.');
        }

        if ($result === 'already_processed') {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Это бронирование уже обработано.');
        }

        return redirect()->route('admin.bookings.index')
            ->with('error', 'Для этого бронирования не найден ожидающий платёж на месте.');
    }

    /**
     * Отмена бронирования администратором.
     */
    public function cancel(Booking $booking): RedirectResponse
    {
        $result = DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($lockedBooking->status, [
                'pending',
                'confirmed',
                'active',
            ], true)) {
                return 'cannot_cancel';
            }

            $lockedBooking->update([
                'status' => 'cancelled',
            ]);

            Payment::where('booking_id', $lockedBooking->id)
                ->where('status', 'pending')
                ->update([
                    'status' => 'cancelled',
                ]);

            return 'success';
        });

        if ($result === 'success') {
            return redirect()->route('admin.bookings.index')
                ->with('success', 'Бронирование №' . $booking->id . ' отменено.');
        }

        return redirect()->route('admin.bookings.index')
            ->with('error', 'Это бронирование уже нельзя отменить.');
    }

    /**
     * Удаление отменённого бронирования и его неоплаченных платежей.
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        $result = DB::transaction(function () use ($booking) {
            $lockedBooking = Booking::whereKey($booking->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedBooking->status !== 'cancelled') {
                return 'not_cancelled';
            }

            $hasPaidPayment = Payment::where('booking_id', $lockedBooking->id)
                ->where('status', 'paid')
                ->exists();

            if ($hasPaidPayment) {
                return 'has_paid_payment';
            }

            Payment::where('booking_id', $lockedBooking->id)->delete();

            $lockedBooking->delete();

            return 'success';
        });

        if ($result === 'success') {
            return redirect()->route('admin.bookings.index')
                ->with('success', 'Бронирование №' . $booking->id . ' и связанные неоплаченные платежи удалены.');
        }

        if ($result === 'not_cancelled') {
            return redirect()->route('admin.bookings.index')
                ->with('error', 'Сначала отмените бронирование, затем его можно будет удалить.');
        }

        return redirect()->route('admin.bookings.index')
            ->with('error', 'У бронирования есть оплаченный платёж. Удаление запрещено, чтобы сохранить историю оплаты.');
    }
}
