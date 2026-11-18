<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Booking::query()
            ->with(['computer.zone', 'computer.room', 'room.zone', 'user'])
            ->latest();

        if (!in_array($request->user()->role, ['admin', 'owner'])) {
            $query->where('user_id', $request->user()->id);
        }

        return BookingResource::collection($query->get());
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
            $booking->load([
                'computer.zone',
                'computer.room',
                'room.zone',
                'user',
            ])
        );
    }

    public function show(Request $request, Booking $booking): BookingResource
    {
        abort_unless(
            in_array($request->user()->role, ['admin', 'owner'])
                || $booking->user_id === $request->user()->id,
            403,
            'Нет доступа к этому бронированию.'
        );

        return new BookingResource(
            $booking->load([
                'computer.zone',
                'computer.room',
                'room.zone',
                'user',
            ])
        );
    }

    public function update(Request $request, Booking $booking)
    {
        abort_unless(
            in_array($request->user()->role, ['admin', 'owner']),
            403,
            'Недостаточно прав.'
        );

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'pending',
                    'confirmed',
                    'active',
                    'completed',
                    'cancelled',
                ]),
            ],
        ]);

        $booking->update($validated);

        return response()->json([
            'message' => 'Статус бронирования обновлён.',
            'data' => new BookingResource(
                $booking->fresh()->load([
                    'computer.zone',
                    'computer.room',
                    'room.zone',
                    'user',
                ])
            ),
        ]);
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
                $booking->load([
                    'computer.zone',
                    'computer.room',
                    'room.zone',
                    'user',
                ])
            ),
        ]);
    }
}
