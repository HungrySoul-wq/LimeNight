@extends('layouts.app')

@section('title', $zone->name . ' — LimeNight')

@section('content')

<section class="min-h-screen bg-black px-4 py-12 text-[#00FA9A] sm:px-8">

    <div class="mx-auto max-w-7xl">

        {{-- Заголовок выбранной зоны --}}
        <div class="mb-10">

            <a href="{{ route('halls.index') }}"
               class="mb-5 inline-block text-sm text-[#00FFFF] transition hover:text-[#B7FF00]">
                ← Все игровые зоны
            </a>

            <h1 class="text-3xl font-black text-[#B7FF00] sm:text-5xl">
                {{ $zone->name }}
            </h1>

            <p class="mt-3 text-[#00FA9A]">
                {{ $zone->description ?? 'Выбери комнату и подходящий компьютер.' }}
            </p>

            {{-- Быстрое бронирование выбранной зоны --}}
            @if($zone->rooms->where('is_active', true)->isNotEmpty())

                <div class="mt-6">
                    <a href="{{ route('booking.create', ['zone_id' => $zone->id, 'now' => 1]) }}"
                       class="inline-block rounded-xl bg-[#B7FF00] px-7 py-4 font-bold text-black transition hover:bg-[#00FFFF]">
                        ⚡ Забронировать сейчас
                    </a>
                </div>

            @endif

        </div>

        {{-- Тарифы выбранной зоны --}}
        <div class="mb-12 grid grid-cols-2 gap-4 md:grid-cols-4">

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-5">
                <p class="text-sm text-gray-400">1 час</p>
                <p class="mt-2 text-2xl font-bold text-[#B7FF00]">
                    {{ number_format($zone->price_1_hour, 0, ',', ' ') }} ₸
                </p>
            </div>

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-5">
                <p class="text-sm text-gray-400">3 часа</p>
                <p class="mt-2 text-2xl font-bold text-[#B7FF00]">
                    {{ number_format($zone->price_3_hours, 0, ',', ' ') }} ₸
                </p>
            </div>

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-5">
                <p class="text-sm text-gray-400">5 часов</p>
                <p class="mt-2 text-2xl font-bold text-[#B7FF00]">
                    {{ number_format($zone->price_5_hours, 0, ',', ' ') }} ₸
                </p>
            </div>

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-5">
                <p class="text-sm text-gray-400">Ночь</p>
                <p class="mt-2 text-2xl font-bold text-[#B7FF00]">
                    {{ number_format($zone->price_night, 0, ',', ' ') }} ₸
                </p>
            </div>

        </div>

        {{-- Комнаты --}}
        <h2 class="mb-6 text-2xl font-bold text-[#B7FF00]">
            Комнаты и компьютеры
        </h2>

        @forelse($zone->rooms as $room)

            @php
                // Определяем, является ли комната Bootcamp.
                $isBootcamp = $room->type === 'bootcamp'
                    || str_contains(mb_strtolower($zone->name), 'bootcamp');
            @endphp

            <div class="mb-8 rounded-2xl border border-[#292929] bg-[#101010] p-5 sm:p-7">

                {{-- Название комнаты --}}
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3">

                    <div>
                        <h3 class="text-xl font-bold text-[#00FFFF]">
                            {{ $room->name }}
                        </h3>

                        <p class="mt-1 text-sm text-gray-400">
                            Компьютеров: {{ $room->computers->count() }}
                        </p>
                    </div>

                    <span class="rounded-full px-4 py-2 text-sm font-bold
                        {{ $room->is_active ? 'bg-[#B7FF00]/10 text-[#B7FF00]' : 'bg-red-500/10 text-red-400' }}">
                        {{ $room->is_active ? 'Активна' : 'Закрыта' }}
                    </span>

                </div>

                {{-- Если комната закрыта --}}
                @if(!$room->is_active)

                    <p class="rounded-xl border border-red-500/30 bg-red-500/5 p-4 text-center font-bold text-red-400">
                        Эта комната временно закрыта.
                    </p>

                {{-- Bootcamp: бронирование всей комнаты --}}
                @elseif($isBootcamp)

                    <div class="rounded-xl border border-[#00FFFF]/30 bg-black p-6 text-center">

                        <p class="mb-2 text-lg font-bold text-[#00FFFF]">
                            Bootcamp — командная комната
                        </p>

                        <p class="mb-5 text-sm leading-6 text-gray-400">
                            Комната рассчитана на команду до 5 игроков.
                            Бронируется сразу вся комната, оплата производится один раз.
                        </p>

                        <a href="{{ route('booking.create', ['zone_id' => $zone->id]) }}"
                           class="inline-block w-full rounded-xl bg-[#B7FF00] px-6 py-4 font-bold text-black transition hover:bg-[#00FFFF] sm:w-auto">
                            Перейти к бронированию Bootcamp
                        </a>

                    </div>

                {{-- Обычные комнаты: выбор отдельного компьютера --}}
                @else

                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">

                        @forelse($room->computers as $computer)

                            @if($computer->status === 'available')

                                {{-- Свободный компьютер --}}
                                <div class="rounded-xl border border-[#B7FF00]/40 bg-[#B7FF00]/5 p-4 text-center">

                                    <div class="mb-2 text-xs text-gray-400">
                                        ПК
                                    </div>

                                    <div class="text-xl font-black text-[#B7FF00]">
                                        {{ $computer->pc_number }}
                                    </div>

                                    <div class="mt-2 text-xs font-bold text-[#B7FF00]">
                                        Свободен
                                    </div>

                                    <a href="{{ route('booking.create', ['zone_id' => $zone->id]) }}"
                                       class="mt-3 block rounded-lg bg-[#B7FF00] px-2 py-2 text-xs font-bold text-black transition hover:bg-[#00FFFF]">
                                        Забронировать
                                    </a>

                                </div>

                            @else

                                {{-- Занятый компьютер --}}
                                <div class="rounded-xl border border-red-500/40 bg-red-500/5 p-4 text-center">

                                    <div class="mb-2 text-xs text-gray-400">
                                        ПК
                                    </div>

                                    <div class="text-xl font-black text-red-400">
                                        {{ $computer->pc_number }}
                                    </div>

                                    <div class="mt-2 text-xs font-bold text-red-400">
                                        Занят
                                    </div>

                                </div>

                            @endif

                        @empty

                            <p class="col-span-full text-sm text-gray-400">
                                В этой комнате пока нет компьютеров.
                            </p>

                        @endforelse

                    </div>

                @endif

            </div>

        @empty

            <p class="rounded-xl border border-[#292929] bg-[#101010] p-6 text-gray-400">
                В этой зоне пока нет комнат.
            </p>

        @endforelse

    </div>

</section>

@endsection