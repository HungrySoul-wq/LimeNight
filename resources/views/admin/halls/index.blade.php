@extends('layouts.app')

@section('title', 'Управление игровыми зонами — LimeNight')

@section('content')

<div class="min-h-screen bg-black px-4 py-10 text-[#00FA9A] sm:px-6">
    <div class="mx-auto max-w-7xl">

        {{-- ВОЗВРАТ В АДМИН-ПАНЕЛЬ --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 font-bold text-[#B7FF00] transition hover:text-[#00FFFF] hover:drop-shadow-[0_0_8px_#B7FF00]">
                <span class="text-xl">←</span>
                Вернуться в админ-панель
            </a>
        </div>

        {{-- ЗАГОЛОВОК --}}
        <div class="mb-8 flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <div>
                <p class="mb-2 text-sm font-bold uppercase tracking-widest text-[#00FFFF]">
                    Администрирование LimeNight
                </p>

                <h1 class="text-3xl font-black text-[#B7FF00] sm:text-4xl">
                    Игровые зоны
                </h1>

                <p class="mt-2 text-gray-400">
                    Управление зонами, тарифами, комнатами и компьютерами.
                </p>
            </div>

            <a href="{{ route('admin.halls.create') }}"
               class="rounded-xl bg-[#B7FF00] px-6 py-3 text-center font-bold text-black transition hover:bg-[#a5e600]">
                + Добавить зону
            </a>
        </div>

        {{-- СООБЩЕНИЯ --}}
        @if(session('success'))
            <div class="mb-6 rounded-xl border border-[#B7FF00] bg-[#101010] p-4 text-[#B7FF00]">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-red-500 bg-[#101010] p-4 text-red-400">
                {{ session('error') }}
            </div>
        @endif

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

        {{-- КОЛИЧЕСТВО ЗОН --}}
        <div class="mb-6 rounded-2xl border border-[#292929] bg-[#101010] p-5">
            <p class="text-sm text-gray-400">Всего игровых зон</p>
            <p class="mt-1 text-3xl font-black text-[#B7FF00]">
                {{ $zones->total() }}
            </p>
        </div>

        {{-- СПИСОК ЗОН --}}
        <div class="space-y-6">

            @forelse($zones as $zone)

                <div class="overflow-hidden rounded-2xl border border-[#292929] bg-[#101010]">

                    {{-- ИНФОРМАЦИЯ О ЗОНЕ --}}
                    <div class="border-b border-[#292929] p-6">

                        <div class="flex flex-col justify-between gap-5 lg:flex-row lg:items-start">

                            <div>
                                <h2 class="text-2xl font-black text-[#B7FF00]">
                                    {{ $zone->name }}
                                </h2>

                                <p class="mt-2 text-sm text-gray-400">
                                    {{ $zone->description ?: 'Описание пока не добавлено.' }}
                                </p>

                                <p class="mt-3 text-sm text-gray-500">
                                    ID зоны: {{ $zone->id }}
                                </p>
                            </div>

                            <div class="flex flex-wrap gap-3">

                                <a href="{{ route('admin.halls.edit', $zone) }}"
                                   class="rounded-lg border border-[#00FFFF] px-4 py-2 text-sm font-bold text-[#00FFFF] transition hover:bg-[#00FFFF] hover:text-black">
                                    Редактировать
                                </a>

                                <form action="{{ route('admin.halls.destroy', $zone) }}"
                                      method="POST"
                                      onsubmit="return confirm('Удалить игровую зону {{ addslashes($zone->name) }}?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="rounded-lg border border-red-500 px-4 py-2 text-sm font-bold text-red-400 transition hover:bg-red-500 hover:text-white">
                                        Удалить
                                    </button>
                                </form>

                            </div>
                        </div>

                        {{-- ТАРИФЫ --}}
                        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">

                            <div class="rounded-xl border border-[#292929] bg-black p-4">
                                <p class="text-xs text-gray-500">Почасовой тариф</p>
                                <p class="mt-2 font-bold text-[#B7FF00]">
                                    {{ number_format((float) $zone->hourly_rate, 0, ',', ' ') }} ₸
                                </p>
                            </div>

                            <div class="rounded-xl border border-[#292929] bg-black p-4">
                                <p class="text-xs text-gray-500">1 час</p>
                                <p class="mt-2 font-bold text-[#B7FF00]">
                                    {{ number_format((float) $zone->price_1_hour, 0, ',', ' ') }} ₸
                                </p>
                            </div>

                            <div class="rounded-xl border border-[#292929] bg-black p-4">
                                <p class="text-xs text-gray-500">3 часа</p>
                                <p class="mt-2 font-bold text-[#B7FF00]">
                                    {{ number_format((float) $zone->price_3_hours, 0, ',', ' ') }} ₸
                                </p>
                            </div>

                            <div class="rounded-xl border border-[#292929] bg-black p-4">
                                <p class="text-xs text-gray-500">5 часов</p>
                                <p class="mt-2 font-bold text-[#B7FF00]">
                                    {{ number_format((float) $zone->price_5_hours, 0, ',', ' ') }} ₸
                                </p>
                            </div>

                            <div class="rounded-xl border border-[#292929] bg-black p-4">
                                <p class="text-xs text-gray-500">Ночной тариф</p>
                                <p class="mt-2 font-bold text-[#B7FF00]">
                                    {{ number_format((float) $zone->price_night, 0, ',', ' ') }} ₸
                                </p>
                            </div>

                        </div>
                    </div>

                    {{-- КОМНАТЫ --}}
                    <div class="p-6">

                        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                            <h3 class="text-lg font-bold text-[#00FFFF]">
                                Комнаты ({{ $zone->rooms->count() }})
                            </h3>
                        </div>

                        @forelse($zone->rooms as $room)

                            <div class="mb-4 rounded-xl border border-[#292929] bg-black p-4 last:mb-0">

                                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">

                                    <div>
                                        <h4 class="font-bold text-[#B7FF00]">
                                            {{ $room->name }}
                                        </h4>

                                        <p class="mt-1 text-sm text-gray-400">
                                            Тип: {{ $room->type ?: 'Не указан' }}
                                            · Вместимость: {{ $room->capacity ?? 'Не указана' }}
                                        </p>

                                        <p class="mt-1 text-sm {{ $room->is_active ? 'text-[#B7FF00]' : 'text-red-400' }}">
                                            {{ $room->is_active ? 'Активна' : 'Неактивна' }}
                                        </p>
                                    </div>

                                    <div class="text-sm text-gray-400">
                                        Компьютеров:
                                        <span class="font-bold text-[#00FFFF]">
                                            {{ $room->computers->count() }}
                                        </span>
                                    </div>

                                </div>

                                {{-- КОМПЬЮТЕРЫ КОМНАТЫ --}}
                                <div class="mt-4 flex flex-wrap gap-2">

                                    @forelse($room->computers as $computer)

                                        @php
                                            $statusColors = [
                                                'available' => 'border-[#B7FF00] text-[#B7FF00]',
                                                'occupied' => 'border-red-500 text-red-400',
                                                'booked' => 'border-orange-400 text-orange-300',
                                                'maintenance' => 'border-gray-500 text-gray-300',
                                            ];

                                            $statusNames = [
                                                'available' => 'Свободен',
                                                'occupied' => 'Занят',
                                                'booked' => 'Забронирован',
                                                'maintenance' => 'Обслуживание',
                                            ];

                                            $status = $computer->status;
                                        @endphp

                                        <span class="rounded-lg border px-3 py-2 text-xs font-semibold {{ $statusColors[$status] ?? 'border-gray-600 text-gray-400' }}">
                                            ПК-{{ $computer->pc_number }}:
                                            {{ $statusNames[$status] ?? $status }}
                                        </span>

                                    @empty

                                        <p class="text-sm text-gray-500">
                                            В этой комнате пока нет компьютеров.
                                        </p>

                                    @endforelse

                                </div>

                            </div>

                        @empty

                            <p class="rounded-xl border border-dashed border-[#292929] p-5 text-center text-sm text-gray-500">
                                В этой зоне пока нет комнат.
                            </p>

                        @endforelse

                    </div>
                </div>

            @empty

                <div class="rounded-2xl border border-dashed border-[#292929] bg-[#101010] p-10 text-center">
                    <p class="text-xl font-bold text-[#B7FF00]">
                        Игровых зон пока нет
                    </p>

                    <p class="mt-2 text-gray-400">
                        Добавьте первую игровую зону.
                    </p>
                </div>

            @endforelse

        </div>

        {{-- ПАГИНАЦИЯ --}}
        <div class="mt-8">
            {{ $zones->links() }}
        </div>

    </div>
</div>

@endsection
