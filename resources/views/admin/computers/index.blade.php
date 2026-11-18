@extends('layouts.app')

@section('title', 'Управление компьютерами — LimeNight')

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
                    Компьютеры
                </h1>
                <p class="mt-2 text-[#00FA9A]">
                    Управление всеми компьютерами клуба LimeNight
                </p>
            </div>

            <a href="{{ route('admin.computers.create') }}"
               class="rounded-xl bg-[#B7FF00] px-6 py-3 text-center font-bold text-black transition hover:bg-[#a5e600]">
                + Добавить ПК
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

        @php
            $statusLabels = [
                'available' => 'Свободен',
                'occupied' => 'Занят',
                'reserved' => 'Забронирован',
                'maintenance' => 'На обслуживании',
            ];

            $statusColors = [
                'available' => 'bg-[#B7FF00]/10 text-[#B7FF00]',
                'occupied' => 'bg-red-500/10 text-red-400',
                'reserved' => 'bg-[#FFB86B]/10 text-[#FFB86B]',
                'maintenance' => 'bg-gray-500/10 text-gray-300',
            ];
        @endphp

        <div class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl border border-[#292929] bg-[#101010] p-4">
                <p class="text-sm text-gray-400">Всего ПК</p>
                <p class="mt-2 text-2xl font-bold text-white">{{ $computers->total() }}</p>
            </div>

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-4">
                <p class="text-sm text-gray-400">Свободны</p>
                <p class="mt-2 text-2xl font-bold text-[#B7FF00]">
                    {{ \App\Models\Computer::where('status', 'available')->count() }}
                </p>
            </div>

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-4">
                <p class="text-sm text-gray-400">Заняты</p>
                <p class="mt-2 text-2xl font-bold text-red-400">
                    {{ \App\Models\Computer::where('status', 'occupied')->count() }}
                </p>
            </div>

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-4">
                <p class="text-sm text-gray-400">Обслуживание</p>
                <p class="mt-2 text-2xl font-bold text-gray-300">
                    {{ \App\Models\Computer::where('status', 'maintenance')->count() }}
                </p>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-[#292929] bg-[#101010]">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-sm">
                    <thead class="border-b border-[#292929] bg-[#171717] text-[#00FA9A]">
                        <tr>
                            <th class="px-5 py-4">Компьютер</th>
                            <th class="px-5 py-4">Зона</th>
                            <th class="px-5 py-4">Комната</th>
                            <th class="px-5 py-4">IP-адрес</th>
                            <th class="px-5 py-4">Статус</th>
                            <th class="px-5 py-4">Действия</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#292929] text-gray-300">
                        @forelse($computers as $computer)
                            <tr class="transition hover:bg-[#171717]">
                                <td class="px-5 py-4 font-bold text-[#B7FF00]">
                                    ПК №{{ $computer->pc_number }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $computer->zone->name ?? '—' }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $computer->room->name ?? 'Без комнаты' }}
                                </td>

                                <td class="px-5 py-4">
                                    {{ $computer->ip_address ?: 'Не указан' }}
                                </td>

                                <td class="px-5 py-4">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$computer->status] ?? 'bg-gray-500/10 text-gray-300' }}">
                                        {{ $statusLabels[$computer->status] ?? $computer->status }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.computers.edit', $computer) }}"
                                           class="font-semibold text-[#00FFFF] hover:underline">
                                            Изменить
                                        </a>

                                        <form action="{{ route('admin.computers.destroy', $computer) }}"
                                              method="POST"
                                              onsubmit="return confirm('Удалить ПК №{{ $computer->pc_number }}?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="font-semibold text-red-400 hover:underline">
                                                Удалить
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                                    Компьютеров пока нет.
                                    <a href="{{ route('admin.computers.create') }}"
                                       class="ml-1 text-[#B7FF00] hover:underline">
                                        Добавить первый ПК
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($computers->hasPages())
            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <p class="text-sm text-[#00FA9A]">
                    Показано с
                    <span class="font-bold text-[#B7FF00]">{{ $computers->firstItem() }}</span>
                    по
                    <span class="font-bold text-[#B7FF00]">{{ $computers->lastItem() }}</span>
                    из
                    <span class="font-bold text-[#B7FF00]">{{ $computers->total() }}</span>
                    компьютеров
                </p>

                <nav class="flex flex-wrap items-center gap-2" aria-label="Навигация по страницам">

                    @if($computers->onFirstPage())
                        <span class="cursor-not-allowed rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-gray-600">
                            ← Предыдущая
                        </span>
                    @else
                        <a href="{{ $computers->previousPageUrl() }}"
                           class="rounded-lg border border-[#B7FF00]/40 bg-[#101010] px-3 py-2 text-sm font-semibold text-[#B7FF00] transition hover:bg-[#B7FF00] hover:text-black">
                            ← Предыдущая
                        </a>
                    @endif

                    @php
                        $startPage = max(1, $computers->currentPage() - 2);
                        $endPage = min($computers->lastPage(), $computers->currentPage() + 2);
                    @endphp

                    @if($startPage > 1)
                        <a href="{{ $computers->url(1) }}"
                           class="rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-[#00FA9A] transition hover:border-[#00FFFF] hover:text-[#00FFFF]">
                            1
                        </a>

                        @if($startPage > 2)
                            <span class="px-1 text-[#00FA9A]">...</span>
                        @endif
                    @endif

                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $computers->currentPage())
                            <span aria-current="page"
                                  class="rounded-lg border border-[#B7FF00] bg-[#B7FF00] px-3 py-2 text-sm font-bold text-black">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $computers->url($page) }}"
                               class="rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-[#00FA9A] transition hover:border-[#00FFFF] hover:text-[#00FFFF]">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if($endPage < $computers->lastPage())
                        @if($endPage < $computers->lastPage() - 1)
                            <span class="px-1 text-[#00FA9A]">...</span>
                        @endif

                        <a href="{{ $computers->url($computers->lastPage()) }}"
                           class="rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-[#00FA9A] transition hover:border-[#00FFFF] hover:text-[#00FFFF]">
                            {{ $computers->lastPage() }}
                        </a>
                    @endif

                    @if($computers->hasMorePages())
                        <a href="{{ $computers->nextPageUrl() }}"
                           class="rounded-lg border border-[#B7FF00]/40 bg-[#101010] px-3 py-2 text-sm font-semibold text-[#B7FF00] transition hover:bg-[#B7FF00] hover:text-black">
                            Следующая →
                        </a>
                    @else
                        <span class="cursor-not-allowed rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-gray-600">
                            Следующая →
                        </span>
                    @endif

                </nav>
            </div>
        @endif

    </div>
</div>
@endsection
