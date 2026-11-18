@extends('layouts.app')

@section('title', 'Управление тарифами — LimeNight')

@section('content')
<div class="min-h-screen bg-black px-4 py-10 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">

        {{-- ВОЗВРАТ В АДМИН-ПАНЕЛЬ --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#00FFFF] transition hover:text-[#B7FF00]">
                ← Назад в админ-панель
            </a>
        </div>

        <div class="mb-8">
            <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-[#B7FF00]">
                Администрирование
            </p>
            <h1 class="text-3xl font-bold text-[#B7FF00] sm:text-4xl">
                Управление тарифами
            </h1>
            <p class="mt-2 text-[#00FA9A]">
                Просмотр и изменение стоимости игровых зон
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-[#B7FF00]/40 bg-[#B7FF00]/10 p-4 text-[#B7FF00]">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-red-400">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            @forelse($zones as $zone)
                <div class="rounded-2xl border border-[#292929] bg-[#101010] p-6 transition hover:border-[#B7FF00]/50">

                    <div class="mb-5 flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-bold text-[#B7FF00]">
                                {{ $zone->name }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-400">
                                {{ $zone->description ?: 'Игровая зона LimeNight' }}
                            </p>
                        </div>

                        <span class="rounded-full bg-[#B7FF00]/10 px-3 py-1 text-xs font-semibold text-[#B7FF00]">
                            ID: {{ $zone->id }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-[#292929] bg-black p-4">
                            <p class="text-xs text-gray-400">1 час</p>
                            <p class="mt-1 text-lg font-bold text-[#00FA9A]">
                                {{ number_format((float) $zone->price_1_hour, 0, ',', ' ') }} ₸
                            </p>
                        </div>

                        <div class="rounded-xl border border-[#292929] bg-black p-4">
                            <p class="text-xs text-gray-400">3 часа</p>
                            <p class="mt-1 text-lg font-bold text-[#00FA9A]">
                                {{ number_format((float) $zone->price_3_hours, 0, ',', ' ') }} ₸
                            </p>
                        </div>

                        <div class="rounded-xl border border-[#292929] bg-black p-4">
                            <p class="text-xs text-gray-400">5 часов</p>
                            <p class="mt-1 text-lg font-bold text-[#00FA9A]">
                                {{ number_format((float) $zone->price_5_hours, 0, ',', ' ') }} ₸
                            </p>
                        </div>

                        <div class="rounded-xl border border-[#292929] bg-black p-4">
                            <p class="text-xs text-gray-400">Ночь</p>
                            <p class="mt-1 text-lg font-bold text-[#00FFFF]">
                                {{ number_format((float) $zone->price_night, 0, ',', ' ') }} ₸
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 border-t border-[#292929] pt-5">
                        <a href="{{ route('admin.prices.edit', $zone) }}"
                           class="block rounded-xl bg-[#B7FF00] px-5 py-3 text-center font-bold text-black transition hover:bg-[#a5e600]">
                            Изменить тарифы
                        </a>
                    </div>

                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-[#292929] bg-[#101010] p-10 text-center">
                    <p class="text-lg font-semibold text-[#B7FF00]">
                        Игровые зоны не найдены
                    </p>
                    <p class="mt-2 text-gray-400">
                        Сначала добавьте игровые зоны в соответствующем разделе.
                    </p>
                </div>
            @endforelse
        </div>

    </div>
</div>
@endsection
