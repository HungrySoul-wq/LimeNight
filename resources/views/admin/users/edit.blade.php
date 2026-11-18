@extends('layouts.app')

@section('title', 'Редактирование пользователя — LimeNight')

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
                Редактирование пользователя
            </h1>
            <p class="mt-2 text-[#00FA9A]">
                Изменение данных аккаунта LimeNight
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
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-5">
                    <label for="name" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                        Имя пользователя
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                        maxlength="255"
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF]"
                    >
                </div>

                <div class="mb-5">
                    <label for="email" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        maxlength="255"
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF]"
                    >
                </div>

                <div class="mb-5">
                    <label for="phone" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                        Телефон
                    </label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        maxlength="30"
                        placeholder="+7 ..."
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF]"
                    >
                </div>

                <div class="mb-6">
                    <label for="role" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                        Роль пользователя
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                        @disabled($user->id === auth()->id())
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none transition focus:border-[#00FFFF] disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="user" @selected(old('role', $user->role) === 'user')>
                            Клиент
                        </option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>
                            Администратор
                        </option>
                        <option value="owner" @selected(old('role', $user->role) === 'owner')>
                            Владелец
                        </option>
                    </select>

                    @if($user->id === auth()->id())
                        <p class="mt-2 text-xs text-[#FFB86B]">
                            Нельзя изменить роль собственной учётной записи.
                        </p>
                        <input type="hidden" name="role" value="{{ $user->role }}">
                    @endif
                </div>

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit"
                            class="rounded-xl bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                        Сохранить изменения
                    </button>

                    <a href="{{ route('admin.users.index') }}"
                       class="rounded-xl border border-[#292929] px-6 py-3 text-center font-semibold text-[#00FFFF] transition hover:bg-[#171717]">
                        Отмена
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
