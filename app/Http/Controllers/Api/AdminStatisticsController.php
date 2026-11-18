<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Computer;
use App\Models\Room;
use App\Models\User;
use App\Models\Zone;

class AdminStatisticsController extends Controller
{
    public function index()
    {
        $activeStatuses = ['pending', 'confirmed', 'active'];

        $recentBookings = Booking::with([
            'user',
            'computer.zone',
            'computer.room',
            'room.zone',
        ])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            // Пользователи
            'users_count' => User::count(),

            // Игровые зоны и комнаты
            'zones_count' => Zone::count(),

            'rooms_count' => Room::count(),

            'active_rooms_count' => Room::where('is_active', true)->count(),

            // Компьютеры
            'computers_count' => Computer::count(),

            'active_computers_count' => Computer::where('status', 'available')
                ->count(),

            'occupied_computers_count' => Computer::where('status', 'occupied')
                ->count(),

            'maintenance_computers_count' => Computer::where('status', 'maintenance')
                ->count(),

            // Бронирования
            'bookings_count' => Booking::count(),

            'active_bookings_count' => Booking::whereIn('status', $activeStatuses)
                ->count(),

            'computer_bookings_count' => Booking::where('booking_type', 'computer')
                ->count(),

            'room_bookings_count' => Booking::where('booking_type', 'room')
                ->count(),

            'completed_bookings_count' => Booking::where('status', 'completed')
                ->count(),

            'cancelled_bookings_count' => Booking::where('status', 'cancelled')
                ->count(),

            'pending_bookings_count' => Booking::where('status', 'pending')
                ->count(),

            // Финансовая статистика
            'revenue' => Booking::whereIn('status', ['completed', 'active'])
                ->sum('total_price'),

            'completed_revenue' => Booking::where('status', 'completed')
                ->sum('total_price'),

            'active_revenue' => Booking::where('status', 'active')
                ->sum('total_price'),

            // Последние бронирования
            'recent_bookings' => $recentBookings,
        ]);
    }
}
