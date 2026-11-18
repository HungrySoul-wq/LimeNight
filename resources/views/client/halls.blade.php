@extends('layouts.app')

@section('title', 'Игровые залы — LimeNight')

@section('content')

{{-- =====================================================
     СТРАНИЦА ИГРОВЫХ ЗОН LIMENIGHT
===================================================== --}}

<section class="mx-auto max-w-7xl px-6 py-14">

    {{-- ЗАГОЛОВОК --}}
    <div class="mb-10 text-center">
        <p class="mb-3 text-sm font-bold uppercase tracking-[4px] text-[#00FFFF]">
            Choose your zone
        </p>

        <h1 class="text-4xl font-black uppercase text-[#B7FF00] sm:text-5xl">
            ИГРОВЫЕ ЗОНЫ
        </h1>

        <p class="mx-auto mt-4 max-w-2xl leading-7 text-[#00FA9A]">
            Выбери подходящую зону, посмотри доступные комнаты
            и забронируй компьютер.
        </p>
    </div>

    {{-- ОБЩАЯ ИНФОРМАЦИЯ --}}
    <div class="mb-14 grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div class="zone-card p-5 text-center">
            <p class="text-3xl font-black text-[#B7FF00]">
                {{ $zones->sum(fn($zone) => $zone->rooms->sum(fn($room) => $room->computers->count())) }}
            </p>
            <p class="mt-2 text-sm text-[#00FA9A]">
                Всего компьютеров
            </p>
        </div>

        <div class="zone-card p-5 text-center">
            <p class="text-3xl font-black text-[#00FFFF]">
                {{ $zones->sum(fn($zone) => $zone->rooms->count()) }}
            </p>
            <p class="mt-2 text-sm text-[#00FA9A]">
                Всего комнат
            </p>
        </div>

        <div class="zone-card p-5 text-center">
            <p class="text-3xl font-black text-[#B7FF00]">
                {{ $zones->count() }}
            </p>
            <p class="mt-2 text-sm text-[#00FA9A]">
                Игровые зоны
            </p>
        </div>

    </div>

    {{-- КАРТОЧКИ ИГРОВЫХ ЗОН И ТАРИФЫ --}}
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3">

        @forelse($zones as $zone)

            <article class="zone-card h-fit self-start overflow-hidden">

                {{-- НАЗВАНИЕ И ОПИСАНИЕ ЗОНЫ --}}
                <div class="border-b border-[#292929] bg-[#151515] p-5">

                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-2xl font-black text-[#B7FF00]">
                            {{ $zone->name }}
                        </h2>

                        <span class="shrink-0 rounded-lg border border-[#00FFFF]/40 px-3 py-2 text-xs font-bold text-[#00FFFF]">
                            {{ $zone->rooms->count() }} комнат
                        </span>
                    </div>

                    <p class="mt-3 text-sm leading-6 text-[#00FA9A]">
                        {{ $zone->description ?: 'Игровая зона компьютерного клуба LimeNight.' }}
                    </p>

                </div>

                <div class="p-5">

                    {{-- КОЛИЧЕСТВО КОМПЬЮТЕРОВ --}}
                    <div class="mb-5 flex items-center justify-between">
                        <span class="text-sm text-[#00FA9A]">
                            Компьютеров в зоне
                        </span>

                        <strong class="text-xl font-black text-[#B7FF00]">
                            {{ $zone->rooms->sum(fn($room) => $room->computers->count()) }}
                        </strong>
                    </div>

                    {{-- ТАРИФЫ --}}
                    <div class="mb-5">

                        <h3 class="mb-3 text-sm font-bold uppercase tracking-widest text-[#00FFFF]">
                            Тарифы
                        </h3>

                        <div class="overflow-hidden rounded-xl border border-[#292929] bg-black">
                            <div class="grid grid-cols-4 divide-x divide-[#292929]">

                                <div class="px-1 py-3 text-center">
                                    <p class="text-xs text-[#00FA9A]/70">1 час</p>
                                    <p class="mt-2 text-sm font-black text-[#B7FF00]">
                                        {{ number_format($zone->price_1_hour, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-xs text-[#00FA9A]">&#8376;</p>
                                </div>

                                <div class="px-1 py-3 text-center">
                                    <p class="text-xs text-[#00FA9A]/70">3 часа</p>
                                    <p class="mt-2 text-sm font-black text-[#B7FF00]">
                                        {{ number_format($zone->price_3_hours, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-xs text-[#00FA9A]">&#8376;</p>
                                </div>

                                <div class="px-1 py-3 text-center">
                                    <p class="text-xs text-[#00FA9A]/70">5 часов</p>
                                    <p class="mt-2 text-sm font-black text-[#B7FF00]">
                                        {{ number_format($zone->price_5_hours, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-xs text-[#00FA9A]">&#8376;</p>
                                </div>

                                <div class="px-1 py-3 text-center">
                                    <p class="text-xs text-[#00FA9A]/70">Ночь</p>
                                    <p class="mt-2 text-sm font-black text-[#B7FF00]">
                                        {{ number_format($zone->price_night, 0, ',', ' ') }}
                                    </p>
                                    <p class="text-xs text-[#00FA9A]">&#8376;</p>
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- КРАТКИЙ СПИСОК КОМНАТ --}}
                    <div class="border-t border-[#292929] pt-5">

                        <h3 class="mb-3 text-sm font-bold uppercase tracking-widest text-[#00FFFF]">
                            Комнаты
                        </h3>

                        <div class="space-y-3">

                            @forelse($zone->rooms as $room)

                                <div class="flex items-center justify-between gap-3 rounded-xl border border-[#292929] bg-black p-4">

                                    <div>
                                        <p class="font-bold text-[#00FA9A]">
                                            {{ $room->name }}
                                        </p>

                                        <p class="mt-1 text-xs text-[#00FA9A]/70">
                                            {{ $room->computers->count() }} компьютеров
                                        </p>
                                    </div>

                                    <span class="shrink-0 text-xs font-bold {{ $room->is_active ? 'text-[#B7FF00]' : 'text-red-400' }}">
                                        {{ $room->is_active ? 'Активна' : 'Закрыта' }}
                                    </span>

                                </div>

                            @empty

                                <p class="text-sm text-[#00FA9A]">
                                    В этой зоне пока нет комнат.
                                </p>

                            @endforelse

                        </div>
                    </div>

                    {{-- КНОПКА ВЫБОРА ЗОНЫ --}}
                    @if($zone->rooms->where('is_active', true)->isNotEmpty())

                        <div class="mt-5">
                            <a href="{{ route('booking.create', ['zone_id' => $zone->id]) }}"
                               class="btn-lime block w-full text-center">
                                Выбрать эту зону
                            </a>
                        </div>

                    @else

                        <div class="mt-5 rounded-xl border border-red-500/30 p-3 text-center text-sm text-red-400">
                            Эта зона временно закрыта
                        </div>

                    @endif

                </div>
            </article>

        @empty

            <div class="col-span-full rounded-xl border border-[#292929] p-10 text-center">
                <h2 class="text-xl font-bold text-[#B7FF00]">
                    Игровые зоны пока не найдены
                </h2>
                <p class="mt-3 text-[#00FA9A]">
                    Попробуй зайти позже.
                </p>
            </div>

        @endforelse

    </div>

</section>


{{-- =====================================================
     ПОДРОБНЫЙ ПРОСМОТР ВСЕХ КОМНАТ
===================================================== --}}

<section id="all-rooms" class="border-y border-[#292929] bg-[#080808]">

    <div class="mx-auto max-w-7xl px-6 py-20">

        {{-- ЗАГОЛОВОК РАЗДЕЛА --}}
        <div class="mb-12 text-center">

            <p class="mb-3 text-sm font-bold uppercase tracking-[4px] text-[#00FFFF]">
                Explore our rooms
            </p>

            <h2 class="text-4xl font-black uppercase text-[#B7FF00] sm:text-5xl">
                ВСЕ КОМНАТЫ КЛУБА
            </h2>

            <p class="mx-auto mt-5 max-w-2xl leading-7 text-[#00FA9A]">
                Посмотри расположение игровых мест, количество компьютеров
                и их текущий статус перед переходом к бронированию.
            </p>

        </div>

        {{-- ОБОЗНАЧЕНИЯ СТАТУСОВ --}}
        <div class="mb-10 flex flex-wrap justify-center gap-5">

            <div class="flex items-center gap-2 text-sm text-[#00FA9A]">
                <span class="h-3 w-3 rounded-full bg-[#B7FF00]"></span>
                Свободен
            </div>

            <div class="flex items-center gap-2 text-sm text-[#00FA9A]">
                <span class="h-3 w-3 rounded-full bg-[#FFB86B]"></span>
                Забронирован
            </div>

            <div class="flex items-center gap-2 text-sm text-[#00FA9A]">
                <span class="h-3 w-3 rounded-full bg-red-500"></span>
                Занят
            </div>

            <div class="flex items-center gap-2 text-sm text-[#00FA9A]">
                <span class="h-3 w-3 rounded-full bg-gray-500"></span>
                На обслуживании
            </div>

        </div>

        {{-- ПЕРЕБОР ВСЕХ ЗОН --}}
        <div class="space-y-12">

            @foreach($zones as $zone)

                <div>

                    {{-- НАЗВАНИЕ ЗОНЫ --}}
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-3 border-b border-[#292929] pb-4">

                        <div>
                            <p class="text-xs font-bold uppercase tracking-widest text-[#00FFFF]">
                                Gaming area
                            </p>

                            <h3 class="mt-2 text-2xl font-black uppercase text-[#B7FF00]">
                                {{ $zone->name }}
                            </h3>
                        </div>

                        <span class="rounded-lg border border-[#292929] px-4 py-2 text-sm text-[#00FA9A]">
                            {{ $zone->rooms->count() }} комнат
                        </span>

                    </div>

                    {{-- КОМНАТЫ ТЕКУЩЕЙ ЗОНЫ --}}
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        @forelse($zone->rooms as $room)

                            @php
                                /*
                                 * Сначала проверяем бронь всей комнаты.
                                 * Это важно для Bootcamp, который бронируется целиком.
                                 */
                                $getComputerDisplayStatus = function ($computer) use ($room) {
                                    $booking = $room->bookings->first()
                                        ?? $computer->bookings->first();

                                    if ($computer->status === 'maintenance') {
                                        return 'maintenance';
                                    }

                                    if (
                                        $computer->status === 'occupied' ||
                                        ($booking && $booking->status === 'active')
                                    ) {
                                        return 'occupied';
                                    }

                                    if (
                                        $booking &&
                                        in_array($booking->status, ['pending', 'confirmed'], true)
                                    ) {
                                        return 'reserved';
                                    }

                                    return 'available';
                                };

                                $availableCount = $room->computers
                                    ->filter(fn($computer) => $getComputerDisplayStatus($computer) === 'available')
                                    ->count();

                                $reservedCount = $room->computers
                                    ->filter(fn($computer) => $getComputerDisplayStatus($computer) === 'reserved')
                                    ->count();

                                $occupiedCount = $room->computers
                                    ->filter(fn($computer) => $getComputerDisplayStatus($computer) === 'occupied')
                                    ->count();

                                $maintenanceCount = $room->computers
                                    ->filter(fn($computer) => $getComputerDisplayStatus($computer) === 'maintenance')
                                    ->count();

                                $isBootcamp = $room->type === 'bootcamp'
                                    || str_contains(mb_strtolower($zone->name), 'bootcamp');
                            @endphp

                            <article class="zone-card overflow-hidden">

                                {{-- ИНФОРМАЦИЯ О КОМНАТЕ --}}
                                <div class="border-b border-[#292929] bg-[#151515] p-6">

                                    <div class="flex flex-wrap items-start justify-between gap-3">

                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-widest text-[#00FFFF]">
                                                {{ $isBootcamp ? 'Team room' : 'Gaming room' }}
                                            </p>

                                            <h4 class="mt-2 text-xl font-black text-[#B7FF00]">
                                                {{ $room->name }}
                                            </h4>
                                        </div>

                                        <span class="rounded-lg border px-3 py-2 text-xs font-bold
                                            {{ $room->is_active
                                                ? 'border-[#B7FF00]/40 text-[#B7FF00]'
                                                : 'border-red-500/40 text-red-400' }}">
                                            {{ $room->is_active ? 'АКТИВНА' : 'ЗАКРЫТА' }}
                                        </span>

                                    </div>

                                    {{-- СТАТИСТИКА КОМПЬЮТЕРОВ --}}
                                    <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-4">

                                        <div class="rounded-lg border border-[#292929] bg-black p-3 text-center">
                                            <p class="text-xl font-black text-white">
                                                {{ $room->computers->count() }}
                                            </p>
                                            <p class="mt-1 text-xs text-[#00FA9A]">
                                                Всего ПК
                                            </p>
                                        </div>

                                        <div class="rounded-lg border border-[#B7FF00]/30 bg-[#B7FF00]/5 p-3 text-center">
                                            <p class="text-xl font-black text-[#B7FF00]">
                                                {{ $availableCount }}
                                            </p>
                                            <p class="mt-1 text-xs text-[#00FA9A]">
                                                Свободно
                                            </p>
                                        </div>

                                        <div class="rounded-lg border border-[#FFB86B]/30 bg-[#FFB86B]/5 p-3 text-center">
                                            <p class="text-xl font-black text-[#FFB86B]">
                                                {{ $reservedCount }}
                                            </p>
                                            <p class="mt-1 text-xs text-[#00FA9A]">
                                                Забронировано
                                            </p>
                                        </div>

                                        <div class="rounded-lg border border-red-500/30 bg-red-500/5 p-3 text-center">
                                            <p class="text-xl font-black text-red-400">
                                                {{ $occupiedCount + $maintenanceCount }}
                                            </p>
                                            <p class="mt-1 text-xs text-[#00FA9A]">
                                                Занято / сервис
                                            </p>
                                        </div>

                                    </div>

                                </div>

                                {{-- СПИСОК КОМПЬЮТЕРОВ --}}
                                <div class="p-6">

                                    <div class="mb-4 flex items-center justify-between">
                                        <h5 class="text-sm font-bold uppercase tracking-widest text-[#00FFFF]">
                                            Игровые места
                                        </h5>

                                        <span class="text-xs text-[#00FA9A]/70">
                                            {{ $room->computers->count() }} ПК
                                        </span>
                                    </div>

                                    @if($room->computers->isNotEmpty())

                                        <div class="grid grid-cols-4 gap-3 sm:grid-cols-4">

                                            @foreach($room->computers->sortBy('pc_number') as $computer)

                                                @php
                                                    $displayStatus = $getComputerDisplayStatus($computer);

                                                    $booking = $room->bookings->first()
                                                        ?? $computer->bookings->first();

                                                    $statusLabel = match ($displayStatus) {
                                                        'reserved' => 'Забронирован',
                                                        'occupied' => 'Занят',
                                                        'maintenance' => 'Сервис',
                                                        default => 'Свободен',
                                                    };

                                                    $statusColor = match ($displayStatus) {
                                                        'reserved' => 'text-[#FFB86B]',
                                                        'occupied' => 'text-red-400',
                                                        'maintenance' => 'text-gray-400',
                                                        default => 'text-[#B7FF00]',
                                                    };

                                                    $statusBorder = match ($displayStatus) {
                                                        'reserved' => 'border-[#FFB86B]/40 bg-[#FFB86B]/5',
                                                        'occupied' => 'border-red-500/40 bg-red-500/5',
                                                        'maintenance' => 'border-[#555555] bg-[#202020]',
                                                        default => 'border-[#B7FF00]/40 bg-[#B7FF00]/5',
                                                    };
                                                @endphp

                                                <div class="rounded-lg border p-3 text-center {{ $statusBorder }}">

                                                    <div class="text-lg font-black {{ $statusColor }}">
                                                        {{ $computer->pc_number }}
                                                    </div>

                                                    <p class="mt-1 text-[10px] font-bold uppercase {{ $statusColor }}">
                                                        {{ $statusLabel }}
                                                    </p>

                                                    @if($displayStatus === 'reserved' && $booking)

                                                        @if($booking->status === 'pending' && $booking->expires_at)
                                                            <p class="mt-2 text-[9px] leading-4 text-[#FFB86B]">
                                                                Оплата до
                                                                {{ $booking->expires_at->format('H:i') }}
                                                            </p>
                                                        @elseif($booking->end_time)
                                                            <p class="mt-2 text-[9px] leading-4 text-[#00FA9A]/70">
                                                                До {{ $booking->end_time->format('d.m H:i') }}
                                                            </p>
                                                        @endif

                                                    @elseif($displayStatus === 'occupied' && $booking && $booking->end_time)

                                                        <p class="mt-2 text-[9px] leading-4 text-red-300">
                                                            До {{ $booking->end_time->format('d.m H:i') }}
                                                        </p>

                                                    @endif

                                                </div>

                                            @endforeach

                                        </div>

                                    @else

                                        <p class="rounded-lg border border-[#292929] bg-black p-5 text-center text-sm text-[#00FA9A]">
                                            В этой комнате пока нет компьютеров.
                                        </p>

                                    @endif

                                    {{-- КНОПКА БРОНИРОВАНИЯ КОМНАТЫ --}}
                                    @if($room->is_active)

                                        <div class="mt-6">
                                            <a href="{{ route('booking.create', ['zone_id' => $zone->id]) }}"
                                               class="btn-lime block w-full text-center">
                                                {{ $isBootcamp ? 'Забронировать комнату Bootcamp' : 'Перейти к бронированию' }}
                                            </a>
                                        </div>

                                    @else

                                        <div class="mt-6 rounded-lg border border-red-500/30 p-3 text-center text-sm text-red-400">
                                            Комната временно недоступна
                                        </div>

                                    @endif

                                </div>

                            </article>

                        @empty

                            <p class="text-sm text-[#00FA9A]">
                                В этой зоне пока нет комнат.
                            </p>

                        @endforelse

                    </div>

                </div>

            @endforeach

        </div>

        {{-- ПОЯСНЕНИЕ --}}
        <div class="mt-12 rounded-xl border border-[#00FFFF]/20 bg-[#00FFFF]/5 p-6">

            <h3 class="font-bold text-[#00FFFF]">
                Обрати внимание
            </h3>

            <p class="mt-3 text-sm leading-7 text-[#00FA9A]">
                Статусы компьютеров учитывают действующие бронирования.
                Неоплаченная бронь отображается как «Забронирован» до окончания
                15-минутного срока оплаты. Подтверждённая бронь сохраняет
                статус до окончания выбранного времени.
            </p>

            <p class="mt-2 text-sm leading-7 text-[#00FA9A]">
                Комната Bootcamp бронируется целиком для команды из пяти игроков,
                поэтому бронь комнаты отображается на всех компьютерах этой комнаты.
            </p>

        </div>

    </div>

</section>

@endsection
