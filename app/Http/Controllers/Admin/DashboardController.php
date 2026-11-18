<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Computer;
use App\Models\Payment;
use App\Models\User;
use App\Models\Zone;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'bookings' => Booking::count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'users' => User::count(),
            'zones' => Zone::count(),
            'computers' => Computer::count(),
            'available_computers' => Computer::where('status', 'available')->count(),
            'revenue' => Payment::where('status', 'paid')->sum('amount'),
        ];

        $latestBookings = Booking::with([
            'user',
            'computer.zone',
            'computer.room',
            'room.zone',
            'payments',
        ])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'latestBookings'));
    }
}
