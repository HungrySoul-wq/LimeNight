@extends('layouts.app')

@section('title', 'Изменение тарифов — LimeNight')

@section('content')
<div class="min-h-screen bg-black px-4 py-10 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-3xl">

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
                Изменение тарифов
            </h1>

            <p class="mt-2 text-[#00FA9A]">
                Игровая зона: {{ $zone->name }}
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-red-400">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="rounded-2xl border border-[#292929] bg-[#101010] p-6 sm:p-8">

            <form action="{{ route('admin.prices.update', $zone) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="price_1_hour" class="mb-2 block font-semibold text-[#00FA9A]">
                        Стоимость 1 часа (₸)
                    </label>
                    <input
                        type="number"
                        id="price_1_hour"
                        name="price_1_hour"
                        value="{{ old('price_1_hour', $zone->price_1_hour) }}"
                        min="0"
                        step="1"
                        required
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF]"
                    >
                </div>

                <div class="mb-6">
                    <label for="price_3_hours" class="mb-2 block font-semibold text-[#00FA9A]">
                        Стоимость 3 часов (₸)
                    </label>
                    <input
                        type="number"
                        id="price_3_hours"
                        name="price_3_hours"
                        value="{{ old('price_3_hours', $zone->price_3_hours) }}"
                        min="0"
                        step="1"
                        required
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF]"
                    >
                </div>

                <div class="mb-6">
                    <label for="price_5_hours" class="mb-2 block font-semibold text-[#00FA9A]">
                        Стоимость 5 часов (₸)
                    </label>
                    <input
                        type="number"
                        id="price_5_hours"
                        name="price_5_hours"
                        value="{{ old('price_5_hours', $zone->price_5_hours) }}"
                        min="0"
                        step="1"
                        required
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF]"
                    >
                </div>

                <div class="mb-8">
                    <label for="price_night" class="mb-2 block font-semibold text-[#00FA9A]">
                        Стоимость ночного тарифа (₸)
                    </label>
                    <input
                        type="number"
                        id="price_night"
                        name="price_night"
                        value="{{ old('price_night', $zone->price_night) }}"
                        min="0"
                        step="1"
                        required
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF]"
                    >
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit"
                            class="rounded-xl bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                        Сохранить тарифы
                    </button>

                    <a href="{{ route('admin.prices.index') }}"
                       class="rounded-xl border border-[#292929] px-6 py-3 text-center font-semibold text-[#00FFFF] transition hover:bg-[#171717]">
                        Отмена
                    </a>
                </div>

            </form>
        </div>

    </div>
</div>
@endsection
