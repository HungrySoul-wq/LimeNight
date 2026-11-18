@extends('layouts.app')

@section('title', 'Бронирование — LimeNight')

@section('content')

<section class="min-h-screen bg-black px-4 py-12 text-[#00FA9A] sm:px-8">
    <div class="mx-auto max-w-6xl">

        {{-- Заголовок страницы --}}
        <div class="mb-10">
            <a href="{{ url('/halls') }}"
               class="text-sm text-[#00FFFF] hover:text-[#B7FF00]">
                ← Все игровые зоны
            </a>

            <h1 class="mt-5 text-3xl font-black text-[#B7FF00] sm:text-5xl">
                Бронирование
            </h1>

            <p class="mt-3 text-gray-400">
                Выбери дату, время и место для игры.
            </p>
        </div>

        {{-- Информация о зоне --}}
        <div class="mb-8 rounded-2xl border border-[#292929] bg-[#101010] p-6">
            <p class="text-sm text-gray-400">Игровая зона</p>

            <h2 class="mt-2 text-2xl font-bold text-[#00FFFF]">
                {{ $zone->name }}
            </h2>

            <p class="mt-2 text-sm text-gray-400">
                {{ $zone->description }}
            </p>
        </div>

        {{-- Сообщение об успешном бронировании --}}
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-[#B7FF00]/40 bg-[#B7FF00]/10 p-4 text-[#B7FF00]">
                {{ session('success') }}
            </div>
        @endif

        {{-- Ошибки формы --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-red-400">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Быстрое заполнение текущей даты и времени --}}
        <div class="mb-4 rounded-2xl border border-[#B7FF00]/30 bg-[#101010] p-5">
            <p class="mb-3 text-sm text-gray-400">
                Хочешь начать игру как можно скорее?
            </p>

            <form action="{{ route('booking.create') }}" method="GET">
                <input type="hidden" name="zone_id" value="{{ $zone->id }}">
                <input type="hidden" name="now" value="1">

                <button type="submit"
                        class="w-full rounded-xl bg-[#B7FF00] px-6 py-4 font-bold text-black transition hover:bg-[#00FFFF] sm:w-auto">
                    ⚡ Забронировать сейчас
                </button>
            </form>

            <p class="mt-3 text-xs text-gray-500">
                Автоматически установит сегодняшнюю дату и ближайшее доступное время по часовому поясу Астаны.
            </p>
        </div>

        {{-- Выбор даты, времени и продолжительности --}}
        <form action="{{ route('booking.create') }}"
              method="GET"
              class="mb-10 rounded-2xl border border-[#292929] bg-[#101010] p-6">

            <input type="hidden" name="zone_id" value="{{ $zone->id }}">

            <h2 class="mb-6 text-xl font-bold text-[#B7FF00]">
                1. Выбери время
            </h2>

            <div class="grid gap-5 sm:grid-cols-3">

                {{-- Дата --}}
                <div>
                    <label class="mb-2 block text-sm text-gray-300">
                        Дата
                    </label>

                    <input type="date"
                           name="date"
                           min="{{ now(config('app.timezone'))->format('Y-m-d') }}"
                           value="{{ $date }}"
                           required
                           class="w-full rounded-lg border border-[#292929] bg-black p-3 text-white">
                </div>

                {{-- Время начала --}}
                <div>
                    <label class="mb-2 block text-sm text-gray-300">
                        Время начала
                    </label>

                    <input type="time"
                           name="time"
                           value="{{ $time }}"
                           required
                           class="w-full rounded-lg border border-[#292929] bg-black p-3 text-white">
                </div>

                {{-- Продолжительность --}}
                <div>
                    <label class="mb-2 block text-sm text-gray-300">
                        Продолжительность
                    </label>

                    <select name="duration"
                            required
                            class="w-full rounded-lg border border-[#292929] bg-black p-3 text-white">

                        <option value="1" @if($duration === '1') selected @endif>
                            1 час
                        </option>

                        <option value="3" @if($duration === '3') selected @endif>
                            3 часа
                        </option>

                        <option value="5" @if($duration === '5') selected @endif>
                            5 часов
                        </option>

                        <option value="night" @if($duration === 'night') selected @endif>
                            Ночь
                        </option>

                    </select>
                </div>

            </div>

            <button type="submit"
                    class="mt-6 rounded-lg bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#00FFFF]">
                Показать доступность
            </button>
        </form>

        {{-- Выбор места --}}
        @if($startTime && $endTime)

            <div class="rounded-2xl border border-[#292929] bg-[#101010] p-6">

                <h2 class="mb-3 text-xl font-bold text-[#B7FF00]">
                    2. Выбери место
                </h2>

                <p class="mb-2 text-sm text-gray-400">
                    Начало: {{ \Illuminate\Support\Carbon::parse($startTime)->format('d.m.Y H:i') }}
                </p>

                <p class="mb-4 text-sm text-gray-400">
                    Окончание: {{ \Illuminate\Support\Carbon::parse($endTime)->format('d.m.Y H:i') }}
                </p>

                {{-- Итоговая стоимость --}}
                <div class="mb-6 rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 p-4">
                    <p class="text-sm text-gray-400">
                        Стоимость выбранного тарифа
                    </p>

                    <p class="mt-1 text-2xl font-black text-[#B7FF00]">
                        {{ number_format((float) $price, 0, ',', ' ') }} ₸
                    </p>
                </div>

                {{-- Обозначения статусов --}}
                <div class="mb-7 rounded-xl border border-[#292929] bg-black p-4">
                    <p class="mb-3 text-sm font-bold text-gray-300">
                        Обозначения компьютеров
                    </p>

                    <div class="flex flex-wrap gap-4 text-sm">

                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#B7FF00]"></span>
                            <span class="text-[#B7FF00]">Свободен</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#FFB86B]"></span>
                            <span class="text-[#FFB86B]">Забронирован</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#FF5555]"></span>
                            <span class="text-[#FF5555]">Занят</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="h-3 w-3 rounded-full bg-[#D1D5DB]"></span>
                            <span class="text-[#D1D5DB]">На обслуживании</span>
                        </div>

                    </div>
                </div>

                @foreach($zone->rooms as $room)

                    <div class="mb-7 rounded-xl border border-[#292929] p-5">

                        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                            <h3 class="font-bold text-[#00FFFF]">
                                {{ $room->name }}
                            </h3>

                            <span class="text-xs text-gray-400">
                                {{ $room->computers->count() }} компьютеров
                            </span>
                        </div>

                        @php
                            $isBootcamp = $room->type === 'bootcamp'
                                || str_contains(mb_strtolower($zone->name), 'bootcamp');

                            $roomBooked = in_array($room->id, $bookedRoomIds)
                                || $room->computers->contains(
                                    fn($computer) => in_array($computer->id, $bookedComputerIds)
                                );

                            $roomReleaseTime = $bookedRoomEndTimes[$room->id] ?? null;
                        @endphp

                        {{-- Bootcamp бронируется целиком --}}
                        @if($isBootcamp)

                            @if(!$room->is_active)

                                <div class="rounded-xl border border-[#D1D5DB]/50 bg-[#D1D5DB]/10 p-4">
                                    <p class="font-bold text-[#D1D5DB]">
                                        Комната закрыта
                                    </p>
                                    <p class="mt-1 text-sm text-[#D1D5DB]">
                                        Время открытия уточняется
                                    </p>
                                </div>

                            @elseif($roomBooked)

                                <div class="rounded-xl border border-[#FFB86B]/60 bg-[#FFB86B]/10 p-4">
                                    <p class="font-bold text-[#FFB86B]">
                                        Комната забронирована
                                    </p>

                                    <p class="mt-1 text-sm text-[#FFB86B]">
                                        Освободится:
                                        @if($roomReleaseTime)
                                            {{ \Illuminate\Support\Carbon::parse($roomReleaseTime)->format('d.m.Y в H:i') }}
                                        @else
                                            Время уточняется
                                        @endif
                                    </p>
                                </div>

                            @else

                                <form action="{{ route('booking.store') }}"
                                      method="POST"
                                      class="rounded-xl border border-[#B7FF00]/20 bg-black p-5">

                                    @csrf

                                    <input type="hidden" name="zone_id" value="{{ $zone->id }}">
                                    <input type="hidden" name="booking_type" value="room">
                                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    <input type="hidden" name="date" value="{{ $date }}">
                                    <input type="hidden" name="time" value="{{ $time }}">
                                    <input type="hidden" name="duration" value="{{ $duration }}">

                                    <p class="mb-4 text-sm text-gray-400">
                                        Вся комната для команды до 5 игроков.
                                        Оплата производится один раз за комнату.
                                    </p>

                                    <p class="mb-3 font-bold text-[#B7FF00]">
                                        Способ оплаты
                                    </p>

                                    <div class="mb-5 grid gap-3 sm:grid-cols-2">

                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[#292929] bg-[#101010] p-4 transition hover:border-[#00FFFF]">
                                            <input type="radio"
                                                   name="payment_method"
                                                   value="card"
                                                   required
                                                   class="accent-[#B7FF00]">
                                            <span>
                                                <span class="block font-bold text-white">
                                                    Банковская карта
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    Демонстрационная онлайн-оплата
                                                </span>
                                            </span>
                                        </label>

                                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[#292929] bg-[#101010] p-4 transition hover:border-[#00FFFF]">
                                            <input type="radio"
                                                   name="payment_method"
                                                   value="onsite"
                                                   required
                                                   class="accent-[#B7FF00]">
                                            <span>
                                                <span class="block font-bold text-white">
                                                    Администратору на месте
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    Оплата в клубе в течение 15 минут
                                                </span>
                                            </span>
                                        </label>

                                    </div>

                                    <button type="submit"
                                            class="w-full rounded-xl bg-[#B7FF00] p-4 font-bold text-black transition hover:bg-[#00FFFF]">
                                        Перейти к бронированию Bootcamp
                                    </button>
                                </form>

                            @endif

                        @else

                            {{-- Выбор отдельных компьютеров --}}
                            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">

                                @foreach($room->computers as $computer)

                                    @php
                                        $computerReleaseTime = $bookedComputerEndTimes[$computer->id]
                                            ?? $bookedRoomEndTimes[$room->id]
                                            ?? null;

                                        $hasBooking = isset($bookedComputerEndTimes[$computer->id])
                                            || isset($bookedRoomEndTimes[$room->id]);

                                        $isMaintenance = $computer->status === 'maintenance';
                                        $isOccupied = $computer->status === 'occupied';
                                        $isBooked = $computer->status === 'booked' || $hasBooking;

                                        $available = $computer->status === 'available'
                                            && !$hasBooking
                                            && $room->is_active;
                                    @endphp

                                    {{-- Компьютер на обслуживании --}}
                                    @if($isMaintenance || !$room->is_active)

                                        <div class="rounded-xl border border-[#D1D5DB]/60 bg-[#D1D5DB]/10 p-4 text-center">
                                            <span class="block text-xs text-gray-400">
                                                ПК
                                            </span>

                                            <span class="my-2 block text-xl font-black text-[#D1D5DB]">
                                                {{ $computer->pc_number }}
                                            </span>

                                            <span class="block text-xs font-bold text-[#D1D5DB]">
                                                На обслуживании
                                            </span>

                                            <span class="mt-2 block text-xs text-[#D1D5DB]">
                                                Время уточняется
                                            </span>
                                        </div>

                                    {{-- Фактически занятый компьютер --}}
                                    @elseif($isOccupied)

                                        <div class="rounded-xl border border-[#FF5555]/60 bg-[#FF5555]/10 p-4 text-center">
                                            <span class="block text-xs text-gray-400">
                                                ПК
                                            </span>

                                            <span class="my-2 block text-xl font-black text-[#FF5555]">
                                                {{ $computer->pc_number }}
                                            </span>

                                            <span class="block text-xs font-bold text-[#FF5555]">
                                                Занят
                                            </span>

                                            <span class="mt-2 block text-xs text-[#FF7777]">
                                                @if($computerReleaseTime)
                                                    Освободится:
                                                    {{ \Illuminate\Support\Carbon::parse($computerReleaseTime)->format('d.m H:i') }}
                                                @else
                                                    Время уточняется
                                                @endif
                                            </span>
                                        </div>

                                    {{-- Забронированный компьютер --}}
                                    @elseif($isBooked)

                                        <div class="rounded-xl border border-[#FFB86B]/60 bg-[#FFB86B]/10 p-4 text-center">
                                            <span class="block text-xs text-gray-400">
                                                ПК
                                            </span>

                                            <span class="my-2 block text-xl font-black text-[#FFB86B]">
                                                {{ $computer->pc_number }}
                                            </span>

                                            <span class="block text-xs font-bold text-[#FFB86B]">
                                                Забронирован
                                            </span>

                                            <span class="mt-2 block text-xs text-[#FFB86B]">
                                                @if($computerReleaseTime)
                                                    Освободится:
                                                    {{ \Illuminate\Support\Carbon::parse($computerReleaseTime)->format('d.m H:i') }}
                                                @else
                                                    Время уточняется
                                                @endif
                                            </span>
                                        </div>

                                    {{-- Свободный компьютер --}}
                                    @elseif($available)

                                        <form action="{{ route('booking.store') }}"
                                              method="POST"
                                              class="col-span-2 rounded-xl border border-[#B7FF00]/30 bg-black p-4 sm:col-span-4 md:col-span-6 lg:col-span-8">

                                            @csrf

                                            <input type="hidden" name="zone_id" value="{{ $zone->id }}">
                                            <input type="hidden" name="booking_type" value="computer">
                                            <input type="hidden" name="computer_id" value="{{ $computer->id }}">
                                            <input type="hidden" name="date" value="{{ $date }}">
                                            <input type="hidden" name="time" value="{{ $time }}">
                                            <input type="hidden" name="duration" value="{{ $duration }}">

                                            <div class="mb-4 text-center">
                                                <span class="block text-xs text-gray-400">
                                                    Выбранный компьютер
                                                </span>

                                                <span class="my-2 block text-2xl font-black text-[#B7FF00]">
                                                    ПК {{ $computer->pc_number }}
                                                </span>

                                                <span class="text-xs font-bold text-[#B7FF00]">
                                                    Свободен
                                                </span>
                                            </div>

                                            <p class="mb-3 text-sm font-bold text-[#B7FF00]">
                                                Способ оплаты
                                            </p>

                                            <div class="mb-5 grid gap-3 sm:grid-cols-2">

                                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[#292929] bg-[#101010] p-4 transition hover:border-[#00FFFF]">
                                                    <input type="radio"
                                                           name="payment_method"
                                                           value="card"
                                                           required
                                                           class="accent-[#B7FF00]">
                                                    <span>
                                                        <span class="block font-bold text-white">
                                                            Банковская карта
                                                        </span>
                                                        <span class="text-xs text-gray-400">
                                                            Демонстрационная онлайн-оплата
                                                        </span>
                                                    </span>
                                                </label>

                                                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-[#292929] bg-[#101010] p-4 transition hover:border-[#00FFFF]">
                                                    <input type="radio"
                                                           name="payment_method"
                                                           value="onsite"
                                                           required
                                                           class="accent-[#B7FF00]">
                                                    <span>
                                                        <span class="block font-bold text-white">
                                                            Администратору на месте
                                                        </span>
                                                        <span class="text-xs text-gray-400">
                                                            Оплата в клубе в течение 15 минут
                                                        </span>
                                                    </span>
                                                </label>

                                            </div>

                                            <button type="submit"
                                                    class="w-full rounded-xl border border-[#B7FF00]/40 bg-[#B7FF00]/5 p-4 font-bold text-[#B7FF00] transition hover:border-[#00FFFF] hover:bg-[#00FFFF]/10 hover:text-[#00FFFF]">
                                                Выбрать этот компьютер
                                            </button>

                                        </form>

                                    {{-- Остальные недоступные состояния --}}
                                    @else

                                        <div class="rounded-xl border border-[#D1D5DB]/60 bg-[#D1D5DB]/10 p-4 text-center">
                                            <span class="block text-xs text-gray-400">
                                                ПК
                                            </span>

                                            <span class="my-2 block text-xl font-black text-[#D1D5DB]">
                                                {{ $computer->pc_number }}
                                            </span>

                                            <span class="block text-xs font-bold text-[#D1D5DB]">
                                                Недоступен
                                            </span>
                                        </div>

                                    @endif

                                @endforeach

                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        @else

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-6 text-center text-gray-400">
                Сначала выбери дату, время и продолжительность.
            </div>

        @endif

    </div>
</section>

@endsection
