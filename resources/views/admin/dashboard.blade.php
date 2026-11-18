@extends('layouts.app')

@section('title', 'Админ-панель — LimeNight')

@section('content')
<div class="min-h-screen bg-black px-4 py-10 sm:px-6 lg:px-10">
    <div class="mx-auto max-w-7xl">

        {{-- ЗАГОЛОВОК --}}
        <div class="mb-10">
            <p class="mb-2 text-sm font-bold uppercase tracking-[0.3em] text-[#00FFFF]">
                LIMENIGHT ADMINISTRATION
            </p>
            <h1 class="text-3xl font-black text-[#B7FF00] sm:text-4xl">
                Панель управления
            </h1>
            <p class="mt-3 text-[#00FA9A]">
                Добро пожаловать в административную систему LimeNight.
            </p>
        </div>

        {{-- СТАТИСТИКА --}}
        <div class="mb-12 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

            @php
                $statistics = [
                    ['Всего бронирований', $stats['bookings'], '#B7FF00'],
                    ['Ожидают оплаты', $stats['pending_bookings'], '#FFB86B'],
                    ['Подтверждённые брони', $stats['confirmed_bookings'], '#00FFFF'],
                    ['Пользователи', $stats['users'], '#B7FF00'],
                    ['Игровые зоны', $stats['zones'], '#00FFFF'],
                    ['Всего компьютеров', $stats['computers'], '#B7FF00'],
                    ['Свободные компьютеры', $stats['available_computers'], '#00FA9A'],
                ];
            @endphp

            @foreach($statistics as $item)
                <div class="rounded-2xl border border-[#292929] bg-[#101010] p-6">
                    <p class="text-sm text-gray-400">{{ $item[0] }}</p>
                    <p class="mt-3 text-3xl font-black" style="color: {{ $item[2] }}">
                        {{ $item[1] }}
                    </p>
                </div>
            @endforeach

            <div class="rounded-2xl border border-[#292929] bg-[#101010] p-6">
                <p class="text-sm text-gray-400">Полученные платежи</p>
                <p class="mt-3 text-3xl font-black text-[#B7FF00]">
                    {{ number_format((float) $stats['revenue'], 0, ',', ' ') }} ₸
                </p>
            </div>
        </div>

        {{-- УПРАВЛЕНИЕ КЛУБОМ --}}
        <div class="mb-6">
            <h2 class="text-2xl font-black text-[#B7FF00]">
                Управление клубом
            </h2>
            <p class="mt-2 text-sm text-gray-400">
                Выберите раздел для управления компьютерным клубом.
            </p>
        </div>

        <div class="mb-12 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">

            {{-- БРОНИРОВАНИЯ --}}
            <a href="{{ route('admin.bookings.index') }}"
               class="group rounded-2xl border border-[#292929] bg-[#101010] p-6 transition hover:border-[#B7FF00] hover:bg-[#171717]">

                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 text-[#B7FF00] transition group-hover:bg-[#B7FF00]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/>
                        <path d="M16 3v4M8 3v4M3 10h18M8 14h2M8 17h2M14 14h2"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-[#B7FF00] transition group-hover:text-[#00FFFF]">
                    Бронирования
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    Просмотр броней и подтверждение оплаты клиентов.
                </p>
                <p class="mt-5 font-bold text-[#00FFFF]">Перейти →</p>
            </a>

            {{-- ИГРОВЫЕ ЗОНЫ --}}
            <a href="{{ route('admin.halls.index') }}"
               class="group rounded-2xl border border-[#292929] bg-[#101010] p-6 transition hover:border-[#B7FF00] hover:bg-[#171717]">

                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 text-[#B7FF00] transition group-hover:bg-[#B7FF00]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M5 21V7l7-4 7 4v14"/>
                        <path d="M9 21v-5h6v5M9 9h.01M15 9h.01M9 12h.01M15 12h.01"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-[#B7FF00] transition group-hover:text-[#00FFFF]">
                    Игровые зоны
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    Создание, редактирование и удаление игровых зон.
                </p>
                <p class="mt-5 font-bold text-[#00FFFF]">Перейти →</p>
            </a>

            {{-- КОМПЬЮТЕРЫ --}}
            <a href="{{ route('admin.computers.index') }}"
               class="group rounded-2xl border border-[#292929] bg-[#101010] p-6 transition hover:border-[#B7FF00] hover:bg-[#171717]">

                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 text-[#B7FF00] transition group-hover:bg-[#B7FF00]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                        <path d="M8 21h8M12 17v4M7 8h10M7 12h5"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-[#B7FF00] transition group-hover:text-[#00FFFF]">
                    Компьютеры
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    Добавление, редактирование, удаление и управление состоянием ПК.
                </p>
                <p class="mt-5 font-bold text-[#00FFFF]">Перейти →</p>
            </a>

            {{-- ПОЛЬЗОВАТЕЛИ --}}
            <a href="{{ route('admin.users.index') }}"
               class="group rounded-2xl border border-[#292929] bg-[#101010] p-6 transition hover:border-[#B7FF00] hover:bg-[#171717]">

                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 text-[#B7FF00] transition group-hover:bg-[#B7FF00]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="10" cy="7" r="4"/>
                        <path d="M20 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-[#B7FF00] transition group-hover:text-[#00FFFF]">
                    Пользователи
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    Просмотр, редактирование и управление аккаунтами клиентов.
                </p>
                <p class="mt-5 font-bold text-[#00FFFF]">Перейти →</p>
            </a>

            {{-- ТАРИФЫ --}}
            <a href="{{ route('admin.prices.index') }}"
               class="group rounded-2xl border border-[#292929] bg-[#101010] p-6 transition hover:border-[#B7FF00] hover:bg-[#171717]">

                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 text-[#B7FF00] transition group-hover:bg-[#B7FF00]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <path d="M2 10h20M6 15h4M17 15h.01"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-[#B7FF00] transition group-hover:text-[#00FFFF]">
                    Тарифы и цены
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    Просмотр и изменение стоимости игровых пакетов.
                </p>
                <p class="mt-5 font-bold text-[#00FFFF]">Перейти →</p>
            </a>

            {{-- СТАТИСТИКА --}}
            <a href="{{ route('admin.statistics.index') }}"
               class="group rounded-2xl border border-[#292929] bg-[#101010] p-6 transition hover:border-[#B7FF00] hover:bg-[#171717]">

                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 text-[#B7FF00] transition group-hover:bg-[#B7FF00]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3v18h18"/>
                        <path d="m7 14 4-4 4 4 6-7"/>
                        <path d="M17 7h4v4"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-[#B7FF00] transition group-hover:text-[#00FFFF]">
                    Статистика
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    Выручка, бронирования, популярность зон и показатели клуба.
                </p>
                <p class="mt-5 font-bold text-[#00FFFF]">Перейти →</p>
            </a>

            {{-- КОМНАТЫ --}}
            <a href="{{ route('admin.rooms.index') }}"
               class="group rounded-2xl border border-[#292929] bg-[#101010] p-6 transition hover:border-[#B7FF00] hover:bg-[#171717]">

                <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 text-[#B7FF00] transition group-hover:bg-[#B7FF00]/10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <path d="M3 9h18M9 21V9M15 9v12M9 14h6"/>
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-[#B7FF00] transition group-hover:text-[#00FFFF]">
                    Игровые комнаты
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    Создание, редактирование и управление комнатами клуба.
                </p>
                <p class="mt-5 font-bold text-[#00FFFF]">Перейти →</p>
            </a>

        </div>

        {{-- ПОСЛЕДНИЕ БРОНИРОВАНИЯ --}}
        <div class="overflow-hidden rounded-2xl border border-[#292929] bg-[#101010]">

            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-[#292929] p-6">
                <div>
                    <h2 class="text-xl font-black text-[#B7FF00]">
                        Последние бронирования
                    </h2>
                    <p class="mt-1 text-sm text-gray-400">
                        Пять последних созданных бронирований.
                    </p>
                </div>

                <a href="{{ route('admin.bookings.index') }}"
                   class="font-bold text-[#00FFFF] transition hover:text-[#B7FF00]">
                    Все бронирования →
                </a>
            </div>

            @forelse($latestBookings as $booking)
                <div class="flex flex-col gap-3 border-b border-[#292929] p-5 last:border-b-0 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <p class="font-bold text-white">
                            Бронирование №{{ $booking->id }}
                        </p>
                        <p class="mt-1 text-sm text-gray-400">
                            {{ $booking->user?->name ?? 'Пользователь не найден' }}
                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $booking->start_time?->format('d.m.Y H:i') ?? 'Дата не указана' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <span class="rounded-full bg-[#171717] px-3 py-1 text-sm text-[#00FA9A]">
                            {{ number_format((float) $booking->total_price, 0, ',', ' ') }} ₸
                        </span>

                        @if($booking->status === 'confirmed')
                            <span class="text-sm font-bold text-[#B7FF00]">Подтверждено</span>
                        @elseif($booking->status === 'pending')
                            <span class="text-sm font-bold text-[#FFB86B]">Ожидает оплаты</span>
                        @elseif($booking->status === 'cancelled')
                            <span class="text-sm font-bold text-[#FF5555]">Отменено</span>
                        @else
                            <span class="text-sm text-gray-400">{{ $booking->status }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-400">
                    Бронирований пока нет.
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
