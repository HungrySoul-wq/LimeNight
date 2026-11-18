<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Computer;
use App\Models\Payment;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_bookings' => Booking::count(),

            'pending_bookings' => Booking::where('status', 'pending')->count(),

            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),

            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),

            'total_users' => User::count(),

            'total_computers' => Computer::count(),

            'available_computers' => Computer::where('status', 'available')->count(),

            'occupied_computers' => Computer::where('status', 'occupied')->count(),

            'maintenance_computers' => Computer::where('status', 'maintenance')->count(),

            'revenue' => Payment::where('status', 'paid')->sum('amount'),

            'total_zones' => Zone::count(),
        ];

        $zoneStats = Zone::withCount('computers')
            ->withCount([
                'rooms as rooms_count',
            ])
            ->get();

        $popularZones = Booking::query()
            ->join('computers', 'bookings.computer_id', '=', 'computers.id')
            ->join('zones', 'computers.zone_id', '=', 'zones.id')
            ->select('zones.id', 'zones.name', DB::raw('COUNT(bookings.id) as bookings_count'))
            ->groupBy('zones.id', 'zones.name')
            ->orderByDesc('bookings_count')
            ->get();

        $latestBookings = Booking::with([
            'user',
            'computer.zone',
            'computer.room',
            'room.zone',
        ])->latest()->take(10)->get();

        return view('admin.statistics.index', compact(
            'stats',
            'zoneStats',
            'popularZones',
            'latestBookings'
        ));
    }
}
