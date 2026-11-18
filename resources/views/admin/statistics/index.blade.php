@extends('layouts.app')

@section('title', 'Статистика — LimeNight')

@section('content')
<div class="min-h-screen bg-black px-4 py-10 text-[#00FA9A] sm:px-6 lg:px-10">

    <div class="mx-auto max-w-7xl">

        {{-- ВОЗВРАТ В АДМИН-ПАНЕЛЬ --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#00FFFF] transition hover:text-[#B7FF00]">
                ← Назад в админ-панель
            </a>
        </div>

        {{-- Заголовок --}}
        <div class="mb-8">
            <div>
                <p class="mb-2 text-sm font-semibold uppercase tracking-[0.3em] text-[#B7FF00]">
                    LimeNight Admin
                </p>

                <h1 class="text-3xl font-black text-[#B7FF00] sm:text-4xl">
                    Статистика клуба
                </h1>

                <p class="mt-2 text-sm text-[#00FA9A]/60">
                    Общая информация о работе компьютерного клуба
                </p>
            </div>
        </div>

        {{-- Бронирования --}}
        <section class="mb-8">
            <h2 class="mb-4 text-xl font-bold text-[#B7FF00]">
                Бронирования
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                @php
                    $bookingCards = [
                        ['title' => 'Всего бронирований', 'value' => $stats['total_bookings'], 'color' => '#B7FF00'],
                        ['title' => 'Ожидают оплаты', 'value' => $stats['pending_bookings'], 'color' => '#FFB86B'],
                        ['title' => 'Подтверждены', 'value' => $stats['confirmed_bookings'], 'color' => '#00FFFF'],
                        ['title' => 'Отменены', 'value' => $stats['cancelled_bookings'], 'color' => '#FF5555'],
                    ];
                @endphp

                @foreach($bookingCards as $card)
                    <div class="rounded-2xl border border-[#292929] bg-[#101010] p-5 transition hover:bg-[#171717]">
                        <p class="text-sm text-[#00FA9A]/60">
                            {{ $card['title'] }}
                        </p>

                        <p class="mt-3 text-3xl font-black"
                           style="color: {{ $card['color'] }}">
                            {{ number_format($card['value'], 0, ',', ' ') }}
                        </p>
                    </div>
                @endforeach

            </div>
        </section>

        {{-- Компьютеры --}}
        <section class="mb-8">
            <h2 class="mb-4 text-xl font-bold text-[#B7FF00]">
                Компьютеры
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                @php
                    $computerCards = [
                        ['title' => 'Всего компьютеров', 'value' => $stats['total_computers'], 'color' => '#00FFFF'],
                        ['title' => 'Свободны', 'value' => $stats['available_computers'], 'color' => '#B7FF00'],
                        ['title' => 'Заняты', 'value' => $stats['occupied_computers'], 'color' => '#FF5555'],
                        ['title' => 'На обслуживании', 'value' => $stats['maintenance_computers'], 'color' => '#D1D5DB'],
                    ];
                @endphp

                @foreach($computerCards as $card)
                    <div class="rounded-2xl border border-[#292929] bg-[#101010] p-5 transition hover:bg-[#171717]">
                        <p class="text-sm text-[#00FA9A]/60">
                            {{ $card['title'] }}
                        </p>

                        <p class="mt-3 text-3xl font-black"
                           style="color: {{ $card['color'] }}">
                            {{ number_format($card['value'], 0, ',', ' ') }}
                        </p>
                    </div>
                @endforeach

            </div>
        </section>

        {{-- Пользователи и доход --}}
        <section class="mb-8">
            <h2 class="mb-4 text-xl font-bold text-[#B7FF00]">
                Общие показатели
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                <div class="rounded-2xl border border-[#292929] bg-[#101010] p-5">
                    <p class="text-sm text-[#00FA9A]/60">
                        Всего пользователей
                    </p>

                    <p class="mt-3 text-3xl font-black text-[#00FFFF]">
                        {{ number_format($stats['total_users'], 0, ',', ' ') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-[#292929] bg-[#101010] p-5">
                    <p class="text-sm text-[#00FA9A]/60">
                        Игровых зон
                    </p>

                    <p class="mt-3 text-3xl font-black text-[#B7FF00]">
                        {{ number_format($stats['total_zones'], 0, ',', ' ') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-[#292929] bg-[#101010] p-5">
                    <p class="text-sm text-[#00FA9A]/60">
                        Полученная выручка
                    </p>

                    <p class="mt-3 text-3xl font-black text-[#B7FF00]">
                        {{ number_format((float) $stats['revenue'], 0, ',', ' ') }}
                        <span class="text-base">₸</span>
                    </p>
                </div>

            </div>
        </section>

        {{-- Состояние зон --}}
        <section class="mb-8">
            <h2 class="mb-4 text-xl font-bold text-[#B7FF00]">
                Игровые зоны и комнаты
            </h2>

            <div class="overflow-hidden rounded-2xl border border-[#292929] bg-[#101010]">

                @if($zoneStats->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[500px] text-left">
                            <thead class="border-b border-[#292929] bg-[#171717]">
                                <tr class="text-sm text-[#00FFFF]">
                                    <th class="px-5 py-4 font-semibold">Игровая зона</th>
                                    <th class="px-5 py-4 font-semibold">Комнаты</th>
                                    <th class="px-5 py-4 font-semibold">Компьютеры</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($zoneStats as $zone)
                                    <tr class="border-b border-[#292929] last:border-0">
                                        <td class="px-5 py-4 font-semibold text-[#B7FF00]">
                                            {{ $zone->name }}
                                        </td>

                                        <td class="px-5 py-4">
                                            {{ $zone->rooms_count }}
                                        </td>

                                        <td class="px-5 py-4">
                                            {{ $zone->computers_count }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="p-6 text-center text-[#00FA9A]/50">
                        Игровые зоны пока не добавлены.
                    </p>
                @endif

            </div>
        </section>

        {{-- Популярность зон --}}
        <section class="mb-8">
            <h2 class="mb-4 text-xl font-bold text-[#B7FF00]">
                Популярность игровых зон
            </h2>

            <div class="rounded-2xl border border-[#292929] bg-[#101010] p-5">

                @if($popularZones->isNotEmpty())
                    <div class="space-y-5">
                        @php
                            $maxBookings = max(1, (int) $popularZones->max('bookings_count'));
                        @endphp

                        @foreach($popularZones as $zone)
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-3">
                                    <span class="font-semibold text-[#00FA9A]">
                                        {{ $zone->name }}
                                    </span>

                                    <span class="text-sm text-[#B7FF00]">
                                        {{ $zone->bookings_count }} бронир.
                                    </span>
                                </div>

                                <div class="h-3 overflow-hidden rounded-full bg-[#292929]">
                                    <div class="h-full rounded-full bg-[#B7FF00]"
                                         style="width: {{ ($zone->bookings_count / $maxBookings) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-[#00FA9A]/50">
                        Данных о бронированиях пока нет.
                    </p>
                @endif

            </div>
        </section>

        {{-- Последние бронирования --}}
        <section class="mb-8">
            <div class="mb-4 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                <h2 class="text-xl font-bold text-[#B7FF00]">
                    Последние бронирования
                </h2>

                <a href="{{ route('admin.bookings.index') }}"
                   class="text-sm font-semibold text-[#00FFFF] transition hover:text-[#B7FF00]">
                    Все бронирования →
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl border border-[#292929] bg-[#101010]">

                @if($latestBookings->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[750px] text-left">
                            <thead class="border-b border-[#292929] bg-[#171717]">
                                <tr class="text-sm text-[#00FFFF]">
                                    <th class="px-5 py-4">№</th>
                                    <th class="px-5 py-4">Клиент</th>
                                    <th class="px-5 py-4">Зона</th>
                                    <th class="px-5 py-4">Компьютер / комната</th>
                                    <th class="px-5 py-4">Стоимость</th>
                                    <th class="px-5 py-4">Статус</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($latestBookings as $booking)
                                    @php
                                        $zone = $booking->computer?->zone
                                            ?? $booking->room?->zone;

                                        $room = $booking->computer?->room
                                            ?? $booking->room;

                                        $statusColors = [
                                            'pending' => '#FFB86B',
                                            'confirmed' => '#B7FF00',
                                            'cancelled' => '#FF5555',
                                            'active' => '#00FFFF',
                                            'completed' => '#D1D5DB',
                                        ];

                                        $statusNames = [
                                            'pending' => 'Ожидает оплаты',
                                            'confirmed' => 'Подтверждено',
                                            'cancelled' => 'Отменено',
                                            'active' => 'Активно',
                                            'completed' => 'Завершено',
                                        ];
                                    @endphp

                                    <tr class="border-b border-[#292929] last:border-0">
                                        <td class="px-5 py-4 text-[#00FFFF]">
                                            #{{ $booking->id }}
                                        </td>

                                        <td class="px-5 py-4">
                                            {{ $booking->user?->name ?? 'Пользователь удалён' }}
                                        </td>

                                        <td class="px-5 py-4">
                                            {{ $zone?->name ?? 'Не указана' }}
                                        </td>

                                        <td class="px-5 py-4">
                                            @if($booking->computer)
                                                ПК №{{ $booking->computer->pc_number }}
                                            @elseif($room)
                                                {{ $room->name }}
                                                <span class="text-xs text-[#B7FF00]">(целиком)</span>
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="px-5 py-4 whitespace-nowrap">
                                            {{ number_format((float) $booking->total_price, 0, ',', ' ') }} ₸
                                        </td>

                                        <td class="px-5 py-4">
                                            <span class="font-semibold"
                                                  style="color: {{ $statusColors[$booking->status] ?? '#00FA9A' }}">
                                                {{ $statusNames[$booking->status] ?? $booking->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="p-6 text-center text-[#00FA9A]/50">
                        Бронирований пока нет.
                    </p>
                @endif

            </div>
        </section>

    </div>
</div>
@endsection
