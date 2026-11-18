{{-- =====================================================
     LIMENIGHT — ШАПКА САЙТА
     Логотип, навигация, вход и регистрация
===================================================== --}}

<header class="sticky top-0 z-50 border-b border-[#292929] bg-black/95 backdrop-blur-md">

    <div class="mx-auto flex max-w-7xl items-center justify-between gap-6 px-6 py-5">

        {{-- ЛОГОТИП --}}
        <a href="{{ route('home') }}" class="shrink-0 text-2xl font-black tracking-wider text-[#B7FF00]">
            LIME<span class="text-[#00FFFF]">NIGHT</span>
        </a>

        {{-- ОСНОВНАЯ НАВИГАЦИЯ ДЛЯ КОМПЬЮТЕРА --}}
        <nav class="hidden items-center gap-8 md:flex">

            <a href="{{ route('home') }}"
               class="text-sm font-semibold text-[#00FA9A] transition hover:text-[#B7FF00]">
                Главная
            </a>

            <a href="{{ route('home') }}#zones"
               class="text-sm font-semibold text-[#00FA9A] transition hover:text-[#B7FF00]">
                Игровые зоны
            </a>

            <a href="{{ route('home') }}#about"
               class="text-sm font-semibold text-[#00FA9A] transition hover:text-[#B7FF00]">
                О клубе
            </a>

            <a href="{{ route('home') }}#contacts"
               class="text-sm font-semibold text-[#00FA9A] transition hover:text-[#B7FF00]">
                Контакты
            </a>

            {{-- АДМИН-ПАНЕЛЬ: ТОЛЬКО ДЛЯ АДМИНА И ВЛАДЕЛЬЦА --}}
            @auth
                @if(in_array(auth()->user()->role, ['admin', 'owner']))
                    <a href="{{ route('admin.bookings.index') }}"
                       class="text-sm font-bold text-[#B7FF00] transition hover:text-[#00FFFF]">
                        Админ-панель
                    </a>
                @endif
            @endauth

        </nav>

        {{-- КНОПКИ АВТОРИЗАЦИИ ДЛЯ КОМПЬЮТЕРА --}}
        <div class="hidden items-center gap-3 sm:flex">

            @guest

                <a href="{{ route('login') }}"
                   class="rounded-xl border border-[#00FFFF] px-5 py-2.5 text-sm font-bold text-[#00FFFF] transition hover:bg-[#00FFFF] hover:text-black">
                    Войти
                </a>

                <a href="{{ route('register') }}"
                   class="rounded-xl bg-[#B7FF00] px-5 py-2.5 text-sm font-bold text-black transition hover:bg-[#a5e600]">
                    Регистрация
                </a>

            @else

                <a href="{{ route('profile') }}"
                   class="rounded-xl border border-[#B7FF00] px-4 py-2.5 text-sm font-bold text-[#B7FF00] transition hover:bg-[#B7FF00] hover:text-black">
                    Личный кабинет
                </a>

                <span class="text-sm font-semibold text-[#00FA9A]">
                    {{ auth()->user()->name }}
                </span>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                            class="text-sm font-bold text-[#00FFFF] transition hover:text-[#B7FF00]">
                        Выйти
                    </button>
                </form>

            @endguest

        </div>

        {{-- КНОПКА ОТКРЫТИЯ МОБИЛЬНОГО МЕНЮ --}}
        <button
            id="mobile-menu-button"
            type="button"
            class="rounded-lg border border-[#B7FF00] px-4 py-2 text-xl text-[#B7FF00] transition hover:bg-[#B7FF00] hover:text-black md:hidden"
            aria-label="Открыть меню"
            aria-expanded="false">
            ☰
        </button>

    </div>

    {{-- МОБИЛЬНОЕ МЕНЮ --}}
    <div id="mobile-menu" class="hidden border-t border-[#292929] bg-[#101010] px-6 py-5 md:hidden">

        <nav class="flex flex-col gap-5">

            <a href="{{ route('home') }}" class="text-[#00FA9A] transition hover:text-[#B7FF00]">
                Главная
            </a>

            <a href="{{ route('home') }}#zones" class="text-[#00FA9A] transition hover:text-[#B7FF00]">
                Игровые зоны
            </a>

            <a href="{{ route('home') }}#about" class="text-[#00FA9A] transition hover:text-[#B7FF00]">
                О клубе
            </a>

            <a href="{{ route('home') }}#contacts" class="text-[#00FA9A] transition hover:text-[#B7FF00]">
                Контакты
            </a>

            {{-- МОБИЛЬНЫЕ КНОПКИ АВТОРИЗАЦИИ --}}
            @guest

                <div class="flex flex-col gap-3 border-t border-[#292929] pt-5">

                    <a href="{{ route('login') }}"
                       class="rounded-xl border border-[#00FFFF] px-4 py-3 text-center font-bold text-[#00FFFF] transition hover:bg-[#00FFFF] hover:text-black">
                        Войти
                    </a>

                    <a href="{{ route('register') }}"
                       class="rounded-xl bg-[#B7FF00] px-4 py-3 text-center font-bold text-black transition hover:bg-[#a5e600]">
                        Регистрация
                    </a>

                </div>

            @else

                <a href="{{ route('profile') }}"
                   class="rounded-xl border border-[#B7FF00] px-4 py-3 text-center font-bold text-[#B7FF00] transition hover:bg-[#B7FF00] hover:text-black">
                    Личный кабинет
                </a>

                <div class="text-[#00FA9A]">
                    {{ auth()->user()->name }}
                </div>

                {{-- АДМИН-ПАНЕЛЬ В МОБИЛЬНОМ МЕНЮ --}}
                @if(in_array(auth()->user()->role, ['admin', 'owner']))
                    <a href="{{ route('admin.bookings.index') }}"
                       class="font-bold text-[#B7FF00]">
                        Админ-панель
                    </a>
                @endif

                <form action="{{ route('logout') }}" method="POST">
                    @csrf

                    <button type="submit"
                            class="w-full rounded-xl border border-[#00FFFF] px-4 py-3 text-center font-bold text-[#00FFFF] transition hover:bg-[#00FFFF] hover:text-black">
                        Выйти
                    </button>
                </form>

            @endguest

        </nav>

    </div>

</header>
