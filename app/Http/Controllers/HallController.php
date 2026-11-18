<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\View\View;

class HallController extends Controller
{
    // Показывает все игровые зоны клуба.
    public function index(): View
    {
        $bookingFilter = function ($query) {
            $query->where(function ($query) {
                $query
                    ->where(function ($pending) {
                        $pending->where('status', 'pending')
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '>', now());
                    })
                    ->orWhere(function ($confirmed) {
                        $confirmed->whereIn('status', ['confirmed', 'active'])
                            ->where('end_time', '>', now());
                    });
            });
        };

        $zones = Zone::with([
            'rooms.computers.bookings' => $bookingFilter,
            'rooms.bookings' => $bookingFilter,
        ])->get();

        return view('client.halls', compact('zones'));
    }

    // Показывает выбранную игровую зону.
    public function show(int $id): View
    {
        $bookingFilter = function ($query) {
            $query->where(function ($query) {
                $query
                    ->where(function ($pending) {
                        $pending->where('status', 'pending')
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '>', now());
                    })
                    ->orWhere(function ($confirmed) {
                        $confirmed->whereIn('status', ['confirmed', 'active'])
                            ->where('end_time', '>', now());
                    });
            });
        };

        $zone = Zone::with([
            'rooms.computers.bookings' => $bookingFilter,
            'rooms.bookings' => $bookingFilter,
        ])->findOrFail($id);

        return view('client.room', compact('zone'));
    }
}
