@extends('layouts.app')

@section('title', 'Управление комнатами — LimeNight')

@section('content')
<div class="min-h-screen bg-black px-4 py-10 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">

        {{-- ВОЗВРАТ В АДМИН-ПАНЕЛЬ --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 font-bold text-[#B7FF00] transition hover:text-[#00FFFF] hover:drop-shadow-[0_0_8px_#B7FF00]">
                <span class="text-xl">←</span>
                Вернуться в админ-панель
            </a>
        </div>

        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-[#B7FF00]">
                    Администрирование
                </p>
                <h1 class="text-3xl font-bold text-[#B7FF00] sm:text-4xl">
                    Игровые комнаты
                </h1>
                <p class="mt-2 text-[#00FA9A]">
                    Управление комнатами и компьютерами клуба LimeNight
                </p>
            </div>

            <a href="{{ route('admin.rooms.create') }}"
               class="inline-flex items-center justify-center rounded-xl bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                + Добавить комнату
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 rounded-xl border border-[#B7FF00]/40 bg-[#B7FF00]/10 p-4 text-[#B7FF00]">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-red-400">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-red-400">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
            @forelse($rooms as $room)
                @php
                    $computers = $room->computers;
                    $available = $computers->where('status', 'available')->count();
                    $occupied = $computers->where('status', 'occupied')->count();
                    $reserved = $computers->where('status', 'reserved')->count();
                    $maintenance = $computers->where('status', 'maintenance')->count();

                    $typeLabels = [
                        'standard' => 'Общий зал',
                        'vip' => 'VIP',
                        'bootcamp' => 'Bootcamp',
                    ];

                    $statusLabels = [
                        'available' => 'Свободен',
                        'occupied' => 'Занят',
                        'reserved' => 'Забронирован',
                        'maintenance' => 'На обслуживании',
                    ];
                @endphp

                <div class="overflow-hidden rounded-2xl border border-[#292929] bg-[#101010]">
                    <div class="border-b border-[#292929] p-5">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div>
                                <h2 class="text-xl font-bold text-[#B7FF00]">
                                    {{ $room->name }}
                                </h2>
                                <p class="mt-1 text-sm text-[#00FA9A]">
                                    {{ $room->zone->name ?? 'Зона не указана' }}
                                </p>
                            </div>

                            @if($room->is_active)
                                <span class="rounded-full bg-[#B7FF00]/10 px-3 py-1 text-xs font-semibold text-[#B7FF00]">
                                    Активна
                                </span>
                            @else
                                <span class="rounded-full bg-red-500/10 px-3 py-1 text-xs font-semibold text-red-400">
                                    Неактивна
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-wrap gap-2 text-xs">
                            <span class="rounded-lg bg-[#171717] px-3 py-2 text-[#00FFFF]">
                                {{ $typeLabels[$room->type] ?? $room->type }}
                            </span>
                            <span class="rounded-lg bg-[#171717] px-3 py-2 text-gray-300">
                                Вместимость: {{ $room->capacity }}
                            </span>
                            <span class="rounded-lg bg-[#171717] px-3 py-2 text-gray-300">
                                ПК: {{ $computers->count() }}
                            </span>
                        </div>
                    </div>

                    <div class="p-5">
                        <h3 class="mb-3 font-semibold text-[#00FA9A]">
                            Компьютеры
                        </h3>

                        <div class="mb-4 grid grid-cols-2 gap-2 text-sm">
                            <div class="rounded-lg bg-[#171717] p-3">
                                <p class="text-xs text-gray-400">Свободны</p>
                                <p class="mt-1 font-bold text-[#B7FF00]">{{ $available }}</p>
                            </div>
                            <div class="rounded-lg bg-[#171717] p-3">
                                <p class="text-xs text-gray-400">Заняты</p>
                                <p class="mt-1 font-bold text-red-400">{{ $occupied }}</p>
                            </div>
                            <div class="rounded-lg bg-[#171717] p-3">
                                <p class="text-xs text-gray-400">Забронированы</p>
                                <p class="mt-1 font-bold text-[#FFB86B]">{{ $reserved }}</p>
                            </div>
                            <div class="rounded-lg bg-[#171717] p-3">
                                <p class="text-xs text-gray-400">Обслуживание</p>
                                <p class="mt-1 font-bold text-gray-300">{{ $maintenance }}</p>
                            </div>
                        </div>

                        @if($computers->isNotEmpty())
                            <div class="mb-5">
                                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-400">
                                    Номера ПК
                                </p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($computers->sortBy('pc_number') as $computer)
                                        @php
                                            $computerColors = [
                                                'available' => 'border-[#B7FF00]/40 text-[#B7FF00]',
                                                'occupied' => 'border-red-500/40 text-red-400',
                                                'reserved' => 'border-[#FFB86B]/40 text-[#FFB86B]',
                                                'maintenance' => 'border-gray-500/40 text-gray-300',
                                            ];
                                        @endphp

                                        <span class="rounded-lg border px-3 py-1 text-xs {{ $computerColors[$computer->status] ?? 'border-gray-600 text-gray-300' }}"
                                              title="{{ $statusLabels[$computer->status] ?? $computer->status }}">
                                            №{{ $computer->pc_number }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="mb-5 text-sm text-gray-500">
                                В этой комнате пока нет компьютеров.
                            </p>
                        @endif

                        <div class="flex flex-wrap gap-2 border-t border-[#292929] pt-4">
                            <a href="{{ route('admin.rooms.edit', $room) }}"
                               class="rounded-lg border border-[#00FFFF]/40 px-4 py-2 text-sm font-semibold text-[#00FFFF] transition hover:bg-[#00FFFF]/10">
                                Редактировать
                            </a>

                            <a href="{{ route('admin.computers.create', ['room_id' => $room->id]) }}"
                               class="rounded-lg border border-[#B7FF00]/40 px-4 py-2 text-sm font-semibold text-[#B7FF00] transition hover:bg-[#B7FF00]/10">
                                + Добавить ПК
                            </a>

                            <form action="{{ route('admin.rooms.destroy', $room) }}"
                                  method="POST"
                                  onsubmit="return confirm('Удалить комнату {{ $room->name }}?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="rounded-lg border border-red-500/40 px-4 py-2 text-sm font-semibold text-red-400 transition hover:bg-red-500/10">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl border border-[#292929] bg-[#101010] p-10 text-center">
                    <h2 class="text-xl font-bold text-[#B7FF00]">
                        Комнат пока нет
                    </h2>
                    <p class="mt-2 text-[#00FA9A]">
                        Добавь первую игровую комнату.
                    </p>
                </div>
            @endforelse
        </div>

        @if($rooms->hasPages())
            <div class="mt-8">
                {{ $rooms->links() }}
            </div>
        @endif

    </div>
</div>
@endsection
