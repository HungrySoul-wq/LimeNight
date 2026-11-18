{{-- =====================================================
     LIMENIGHT — СТРАНИЦА ВХОДА
     Авторизация пользователей
===================================================== --}}

@extends('layouts.app')

@section('title', 'Вход — LimeNight')

@section('content')

<section class="flex min-h-[80vh] items-center justify-center bg-black px-4 py-16">

    <div class="w-full max-w-md rounded-2xl border border-[#292929] bg-[#101010] p-8 shadow-2xl">

        {{-- ЗАГОЛОВОК --}}
        <div class="mb-8 text-center">

            <a href="{{ route('home') }}"
               class="text-3xl font-black tracking-wider text-[#B7FF00]">
                LIME<span class="text-[#00FFFF]">NIGHT</span>
            </a>

            <h1 class="mt-6 text-2xl font-black text-[#B7FF00]">
                С возвращением!
            </h1>

            <p class="mt-2 text-sm text-gray-400">
                Войди в свой аккаунт LimeNight
            </p>

        </div>

        {{-- СООБЩЕНИЕ ОБ ОШИБКАХ --}}
        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-400">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- ФОРМА ВХОДА --}}
        <form action="{{ route('login.store') }}" method="POST" class="space-y-5">

            @csrf

            {{-- EMAIL --}}
            <div>
                <label for="email" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                    Электронная почта
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="example@mail.com"
                    autocomplete="email"
                    required
                    class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none transition focus:border-[#B7FF00]"
                >
            </div>

            {{-- ПАРОЛЬ --}}
            <div>
                <label for="password" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                    Пароль
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Введите пароль"
                    autocomplete="current-password"
                    required
                    class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none transition focus:border-[#B7FF00]"
                >
            </div>

            {{-- ЗАПОМНИТЬ МЕНЯ --}}
            <div class="flex items-center gap-2">
                <input
                    type="checkbox"
                    id="remember"
                    name="remember"
                    value="1"
                    class="h-4 w-4 accent-[#B7FF00]"
                >

                <label for="remember" class="text-sm text-gray-400">
                    Запомнить меня
                </label>
            </div>

            {{-- КНОПКА ВХОДА --}}
            <button
                type="submit"
                class="w-full rounded-xl bg-[#B7FF00] py-3 font-black text-black transition hover:bg-[#a5e600]">
                Войти в аккаунт
            </button>

        </form>

        {{-- ПЕРЕХОД К РЕГИСТРАЦИИ --}}
        <div class="mt-7 border-t border-[#292929] pt-6 text-center">

            <p class="text-sm text-gray-400">
                Ещё нет аккаунта?
            </p>

            <a href="{{ route('register') }}"
               class="mt-2 inline-block font-bold text-[#00FFFF] transition hover:text-[#B7FF00]">
                Зарегистрироваться
            </a>

        </div>

    </div>

</section>

@endsection
