@extends('layouts.app')

@section('title', 'Регистрация — LimeNight')

@section('content')

{{-- Страница регистрации --}}
<section class="flex min-h-[80vh] items-center justify-center px-5 py-16">

    <div class="zone-card w-full max-w-lg p-8 sm:p-10">

        {{-- Заголовок --}}
        <div class="mb-8 text-center">

            <p class="mb-3 text-sm font-bold uppercase tracking-[4px] text-[#00FFFF]">
                Join LimeNight
            </p>

            <h1 class="text-3xl font-black uppercase text-[#B7FF00]">
                Регистрация
            </h1>

            <p class="mt-3 text-sm text-[#00FA9A]">
                Создай аккаунт и бронируй игровые места.
            </p>

        </div>

        {{-- Ошибки заполнения формы --}}
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-sm text-red-400">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Форма регистрации --}}
        <form action="{{ route('register.store') }}" method="POST" class="space-y-5">

            @csrf

            {{-- Имя --}}
            <div>
                <label for="name" class="mb-2 block text-sm font-bold text-[#00FA9A]">
                    Имя
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    placeholder="Введите ваше имя"
                    class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none transition placeholder:text-[#00FA9A]/40 focus:border-[#B7FF00]"
                >
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="mb-2 block text-sm font-bold text-[#00FA9A]">
                    Электронная почта
                </label>

                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    placeholder="example@mail.com"
                    class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none transition placeholder:text-[#00FA9A]/40 focus:border-[#00FFFF]"
                >
            </div>

            
        
        <div>
            <label for="phone" class="mb-2 block text-sm font-medium text-[#00FA9A]">
              Номер телефона
          </label>

         <input
                type="tel"
                id="phone"
                name="phone"
                value="{{ old('phone') }}"
                placeholder="+7 777 123 45 67"
                autocomplete="tel"
                inputmode="tel"
                pattern="[+]?[0-9 ]{10,16}"
                maxlength="16"
                required
               oninput="this.value = this.value.replace(/[^0-9+ ]/g, '').replace(/(?!^)\+/g, '')"
               class="w-full rounded-xl border border-[#292929] bg-[#101010] px-4 py-3 text-[#00FA9A] outline-none transition focus:border-[#B7FF00]"
         >

            @error('phone')
             <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
         @enderror
        </div>



            {{-- Пароль --}}
            <div>
                <label for="password" class="mb-2 block text-sm font-bold text-[#00FA9A]">
                    Пароль
                </label>

                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Минимум 8 символов"
                    class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none transition placeholder:text-[#00FA9A]/40 focus:border-[#B7FF00]"
                >
            </div>

            {{-- Подтверждение пароля --}}
            <div>
                <label for="password_confirmation" class="mb-2 block text-sm font-bold text-[#00FA9A]">
                    Повторите пароль
                </label>

                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    placeholder="Повторите пароль"
                    class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-[#00FA9A] outline-none transition placeholder:text-[#00FA9A]/40 focus:border-[#B7FF00]"
                >
            </div>

            {{-- Кнопка регистрации --}}
            <button type="submit" class="btn-lime w-full cursor-pointer">
                Создать аккаунт
            </button>

        </form>

        {{-- Переход ко входу --}}
        <div class="mt-7 border-t border-[#292929] pt-6 text-center">

            <p class="text-sm text-[#00FA9A]">
                Уже есть аккаунт?
                <a href="{{ route('login') }}" class="font-bold text-[#00FFFF] hover:text-[#B7FF00]">
                    Войти
                </a>
            </p>

        </div>

    </div>

</section>

@endsection
