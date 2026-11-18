@extends('layouts.app')

@section('title', 'Управление пользователями — LimeNight')

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

        {{-- ЗАГОЛОВОК --}}
        <div class="mb-8">
            <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-[#B7FF00]">
                Администрирование
            </p>
            <h1 class="text-3xl font-bold text-[#B7FF00] sm:text-4xl">
                Пользователи
            </h1>
            <p class="mt-2 text-[#00FA9A]">
                Управление аккаунтами клиентов и сотрудников LimeNight
            </p>
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

        {{-- СТАТИСТИКА --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-[#292929] bg-[#101010] p-5">
                <p class="text-sm text-gray-400">Всего пользователей</p>
                <p class="mt-2 text-3xl font-bold text-[#B7FF00]">
                    {{ $stats['total'] }}
                </p>
            </div>

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-5">
                <p class="text-sm text-gray-400">Клиенты</p>
                <p class="mt-2 text-3xl font-bold text-[#00FA9A]">
                    {{ $stats['clients'] }}
                </p>
            </div>

            <div class="rounded-xl border border-[#292929] bg-[#101010] p-5">
                <p class="text-sm text-gray-400">Администраторы и владельцы</p>
                <p class="mt-2 text-3xl font-bold text-[#00FFFF]">
                    {{ $stats['admins'] }}
                </p>
            </div>
        </div>

        {{-- ПОИСК И ФИЛЬТР --}}
        <form action="{{ route('admin.users.index') }}" method="GET"
              class="mb-6 rounded-2xl border border-[#292929] bg-[#101010] p-5">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label for="search" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                        Поиск пользователя
                    </label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Имя, email или телефон"
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF]"
                    >
                </div>

                <div>
                    <label for="role" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                        Роль
                    </label>
                    <select
                        id="role"
                        name="role"
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none focus:border-[#00FFFF]"
                    >
                        <option value="">Все роли</option>
                        <option value="user" @selected(request('role') === 'user')>Клиент</option>
                        <option value="admin" @selected(request('role') === 'admin')>Администратор</option>
                        <option value="owner" @selected(request('role') === 'owner')>Владелец</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                            class="flex-1 rounded-xl bg-[#B7FF00] px-4 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                        Найти
                    </button>

                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-xl border border-[#292929] px-4 py-3 font-semibold text-[#00FFFF] transition hover:bg-[#171717]">
                        Сбросить
                    </a>
                </div>
            </div>
        </form>

        {{-- ТАБЛИЦА ПОЛЬЗОВАТЕЛЕЙ --}}
        <div class="overflow-hidden rounded-2xl border border-[#292929] bg-[#101010]">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left text-sm">
                    <thead class="border-b border-[#292929] bg-[#171717] text-[#00FA9A]">
                        <tr>
                            <th class="px-5 py-4">Пользователь</th>
                            <th class="px-5 py-4">Email</th>
                            <th class="px-5 py-4">Телефон</th>
                            <th class="px-5 py-4">Роль</th>
                            <th class="px-5 py-4">Регистрация</th>
                            <th class="px-5 py-4">Действия</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-[#292929] text-gray-300">
                        @forelse($users as $user)
                            <tr class="transition hover:bg-[#171717]">
                                <td class="px-5 py-4 font-semibold text-[#B7FF00]">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span class="ml-1 text-xs text-[#00FFFF]">(Вы)</span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">{{ $user->email }}</td>

                                <td class="px-5 py-4">{{ $user->phone ?: 'Не указан' }}</td>

                                <td class="px-5 py-4">
                                    @if($user->role === 'owner')
                                        <span class="rounded-full bg-[#00FFFF]/10 px-3 py-1 text-xs font-semibold text-[#00FFFF]">
                                            Владелец
                                        </span>
                                    @elseif($user->role === 'admin')
                                        <span class="rounded-full bg-[#B7FF00]/10 px-3 py-1 text-xs font-semibold text-[#B7FF00]">
                                            Администратор
                                        </span>
                                    @else
                                        <span class="rounded-full bg-[#292929] px-3 py-1 text-xs font-semibold text-gray-300">
                                            Клиент
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    {{ $user->created_at?->format('d.m.Y') ?? '—' }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                           class="font-semibold text-[#00FFFF] hover:underline">
                                            Изменить
                                        </a>

                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('admin.users.destroy', $user) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Удалить пользователя {{ addslashes($user->name) }}?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="font-semibold text-red-400 hover:underline">
                                                    Удалить
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                                    Пользователи не найдены.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ПАГИНАЦИЯ --}}
        @if($users->hasPages())
            <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-[#00FA9A]">
                    Показано с
                    <span class="font-bold text-[#B7FF00]">{{ $users->firstItem() }}</span>
                    по
                    <span class="font-bold text-[#B7FF00]">{{ $users->lastItem() }}</span>
                    из
                    <span class="font-bold text-[#B7FF00]">{{ $users->total() }}</span>
                    пользователей
                </p>

                <nav class="flex flex-wrap items-center gap-2" aria-label="Навигация по страницам">
                    @if($users->onFirstPage())
                        <span class="cursor-not-allowed rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-gray-600">
                            ← Предыдущая
                        </span>
                    @else
                        <a href="{{ $users->previousPageUrl() }}"
                           class="rounded-lg border border-[#B7FF00]/40 bg-[#101010] px-3 py-2 text-sm font-semibold text-[#B7FF00] transition hover:bg-[#B7FF00] hover:text-black">
                            ← Предыдущая
                        </a>
                    @endif

                    @php
                        $startPage = max(1, $users->currentPage() - 2);
                        $endPage = min($users->lastPage(), $users->currentPage() + 2);
                    @endphp

                    @if($startPage > 1)
                        <a href="{{ $users->url(1) }}"
                           class="rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-[#00FA9A] hover:border-[#00FFFF] hover:text-[#00FFFF]">
                            1
                        </a>

                        @if($startPage > 2)
                            <span class="px-1 text-[#00FA9A]">...</span>
                        @endif
                    @endif

                    @for($page = $startPage; $page <= $endPage; $page++)
                        @if($page == $users->currentPage())
                            <span aria-current="page"
                                  class="rounded-lg border border-[#B7FF00] bg-[#B7FF00] px-3 py-2 text-sm font-bold text-black">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $users->url($page) }}"
                               class="rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-[#00FA9A] hover:border-[#00FFFF] hover:text-[#00FFFF]">
                                {{ $page }}
                            </a>
                        @endif
                    @endfor

                    @if($endPage < $users->lastPage())
                        @if($endPage < $users->lastPage() - 1)
                            <span class="px-1 text-[#00FA9A]">...</span>
                        @endif

                        <a href="{{ $users->url($users->lastPage()) }}"
                           class="rounded-lg border border-[#292929] bg-[#101010] px-3 py-2 text-sm text-[#00FA9A] hover:border-[#00FFFF] hover:text-[#00FFFF]">
                            {{ $users->lastPage() }}
                        </a>
                    @endif

                    @if($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}"
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
