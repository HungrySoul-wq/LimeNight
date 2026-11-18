@extends('layouts.app')

@section('title', 'Редактирование игровой зоны — LimeNight')

@section('content')

<div class="min-h-screen bg-black px-4 py-10 text-[#00FA9A] sm:px-6">
    <div class="mx-auto max-w-4xl">

        {{-- ВОЗВРАТ В АДМИН-ПАНЕЛЬ --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#00FFFF] transition hover:text-[#B7FF00]">
                ← Назад в админ-панель
            </a>
        </div>

        {{-- ЗАГОЛОВОК --}}
        <div class="mb-8">
            <p class="text-sm font-bold uppercase tracking-widest text-[#00FFFF]">
                Администрирование LimeNight
            </p>

            <h1 class="mt-2 text-3xl font-black text-[#B7FF00] sm:text-4xl">
                Редактирование зоны
            </h1>

            <p class="mt-2 text-gray-400">
                Изменение информации и тарифов зоны «{{ $zone->name }}».
            </p>
        </div>

        {{-- ОШИБКИ --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-500 bg-[#101010] p-4 text-red-400">
                <p class="mb-2 font-bold">Проверьте введённые данные:</p>

                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ФОРМА --}}
        <form action="{{ route('admin.halls.update', $zone) }}"
              method="POST"
              class="space-y-8 rounded-2xl border border-[#292929] bg-[#101010] p-6 sm:p-8">

            @csrf
            @method('PUT')

            {{-- ОСНОВНАЯ ИНФОРМАЦИЯ --}}
            <div>
                <h2 class="mb-5 text-xl font-bold text-[#00FFFF]">
                    Основная информация
                </h2>

                <div class="space-y-5">

                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold">
                            Название игровой зоны *
                        </label>

                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $zone->name) }}"
                            required
                            maxlength="255"
                            class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none transition focus:border-[#B7FF00]"
                        >
                    </div>

                    <div>
                        <label for="description" class="mb-2 block text-sm font-semibold">
                            Описание зоны
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="4"
                            class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none transition focus:border-[#B7FF00]"
                        >{{ old('description', $zone->description) }}</textarea>
                    </div>

                </div>
            </div>

            {{-- ТАРИФЫ --}}
            <div>
                <h2 class="mb-2 text-xl font-bold text-[#00FFFF]">
                    Стоимость посещения
                </h2>

                <p class="mb-5 text-sm text-gray-400">
                    Все цены указываются в тенге.
                </p>

                <div class="grid gap-5 sm:grid-cols-2">

                    <div>
                        <label for="hourly_rate" class="mb-2 block text-sm font-semibold">
                            Почасовой тариф (₸)
                        </label>

                        <input
                            id="hourly_rate"
                            name="hourly_rate"
                            type="number"
                            min="0"
                            step="0.01"
                            value="{{ old('hourly_rate', $zone->hourly_rate) }}"
                            class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none focus:border-[#B7FF00]"
                        >
                    </div>

                    <div>
                        <label for="price_1_hour" class="mb-2 block text-sm font-semibold">
                            Пакет 1 час (₸)
                        </label>

                        <input
                            id="price_1_hour"
                            name="price_1_hour"
                            type="number"
                            min="0"
                            step="0.01"
                            value="{{ old('price_1_hour', $zone->price_1_hour) }}"
                            class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none focus:border-[#B7FF00]"
                        >
                    </div>

                    <div>
                        <label for="price_3_hours" class="mb-2 block text-sm font-semibold">
                            Пакет 3 часа (₸)
                        </label>

                        <input
                            id="price_3_hours"
                            name="price_3_hours"
                            type="number"
                            min="0"
                            step="0.01"
                            value="{{ old('price_3_hours', $zone->price_3_hours) }}"
                            class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none focus:border-[#B7FF00]"
                        >
                    </div>

                    <div>
                        <label for="price_5_hours" class="mb-2 block text-sm font-semibold">
                            Пакет 5 часов (₸)
                        </label>

                        <input
                            id="price_5_hours"
                            name="price_5_hours"
                            type="number"
                            min="0"
                            step="0.01"
                            value="{{ old('price_5_hours', $zone->price_5_hours) }}"
                            class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none focus:border-[#B7FF00]"
                        >
                    </div>

                    <div>
                        <label for="price_night" class="mb-2 block text-sm font-semibold">
                            Ночной тариф (₸)
                        </label>

                        <input
                            id="price_night"
                            name="price_night"
                            type="number"
                            min="0"
                            step="0.01"
                            value="{{ old('price_night', $zone->price_night) }}"
                            class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none focus:border-[#B7FF00]"
                        >
                    </div>

                </div>
            </div>

            {{-- КНОПКИ --}}
            <div class="flex flex-col gap-3 border-t border-[#292929] pt-6 sm:flex-row">

                <button
                    type="submit"
                    class="rounded-xl bg-[#B7FF00] px-7 py-3 font-bold text-black transition hover:bg-[#a5e600)">
                    Сохранить изменения
                </button>

                <a href="{{ route('admin.halls.index') }}"
                   class="rounded-xl border border-[#292929] px-7 py-3 text-center font-bold text-gray-300 transition hover:border-[#00FFFF] hover:text-[#00FFFF]">
                    Отмена
                </a>

            </div>

        </form>

    </div>
</div>

@endsection
