<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpireUnpaidBookings extends Command
{
    /**
     * Имя команды для терминала.
     */
    protected $signature = 'bookings:expire-unpaid';

    /**
     * Описание команды.
     */
    protected $description = 'Отменяет бронирования с истёкшим сроком оплаты';

    /**
     * Выполнение команды.
     */
    public function handle(): int
    {
        $expiredCount = 0;

        $bookings = Booking::where('status', 'pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($bookings as $booking) {

            DB::transaction(function () use ($booking, &$expiredCount) {

                $lockedBooking = Booking::whereKey($booking->id)
                    ->lockForUpdate()
                    ->first();

                if (
                    !$lockedBooking ||
                    $lockedBooking->status !== 'pending' ||
                    !$lockedBooking->expires_at ||
                    $lockedBooking->expires_at->isFuture()
                ) {
                    return;
                }

                $lockedBooking->update([
                    'status' => 'cancelled',
                ]);

                $lockedBooking->payments()
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'expired',
                    ]);

                $expiredCount++;
            });
        }

        $this->info("Отменено просроченных бронирований: {$expiredCount}");

        return self::SUCCESS;
    }
}
