<?php

namespace App\Http\Controllers;

use App\Models\Zone;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Главная страница LimeNight.
     *
     * Получаем из базы зоны, комнаты и компьютеры.
     * Передаём данные в Blade-шаблон client.home.
     */
    public function index(): View
    {
        // Получаем все зоны вместе с комнатами и компьютерами.
        $zones = Zone::with(['rooms.computers'])
            ->get();

        // Считаем общее количество компьютеров.
        $totalComputers = $zones->sum(
            fn ($zone) => $zone->computers->count()
        );

        // Передаём данные на главную страницу.
        return view('client.home', compact(
            'zones',
            'totalComputers'
        ));
    }
}
