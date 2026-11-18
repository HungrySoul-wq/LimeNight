<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $bookings = Booking::query()
            ->where('user_id', $request->user()->id)
            ->with(['computer.zone', 'user'])
            ->latest()
            ->get();

        return BookingResource::collection($bookings);
    }

    public function store(
        StoreBookingRequest $request,
        BookingService $bookingService
    ): BookingResource {
        $booking = $bookingService->createBooking(
            $request->user(),
            $request->validated()
        );

        return new BookingResource(
            $booking->load(['computer.zone', 'user'])
        );
    }

    public function show(Request $request, Booking $booking): BookingResource
    {
        abort_unless(
            $booking->user_id === $request->user()->id,
            403,
            'Нет доступа к этому бронированию.'
        );

        return new BookingResource(
            $booking->load(['computer.zone', 'user'])
        );
    }

    public function destroy(
        Request $request,
        Booking $booking,
        BookingService $bookingService
    ) {
        $booking = $bookingService->cancelBooking(
            $request->user(),
            $booking
        );

        return response()->json([
            'message' => 'Бронирование отменено.',
            'refund_amount' => $booking->getAttribute('refund_amount'),
            'booking' => new BookingResource(
                $booking->load(['computer.zone', 'user'])
            ),
        ]);
    }
}
