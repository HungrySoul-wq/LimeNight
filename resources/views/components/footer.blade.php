{{-- =====================================================
     LIMENIGHT — ПОДВАЛ САЙТА
     Логотип, навигация, информация и контакты
===================================================== --}}

<footer class="mt-24 border-t border-[#292929] bg-[#050505]">

    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-12 px-6 py-14 sm:grid-cols-2 lg:grid-cols-4">

        {{-- ЛОГОТИП И ОПИСАНИЕ --}}
        <div>
            <a href="{{ route('home') }}"
               class="text-3xl font-black tracking-wider text-[#B7FF00]">
                LIME<span class="text-[#00FFFF]">NIGHT</span>
            </a>

            <p class="mt-5 max-w-xs text-sm leading-7 text-[#00FA9A]">
                Компьютерный клуб нового поколения.
                Твоя игра. Твои правила. Твой LimeNight.
            </p>
        </div>

        {{-- НАВИГАЦИЯ --}}
        <div>
            <h3 class="mb-5 text-lg font-bold text-[#B7FF00]">
                Навигация
            </h3>

            <ul class="space-y-3 text-sm">
                <li>
                    <a href="{{ route('home') }}" class="text-[#00FA9A] transition hover:text-[#00FFFF]">
                        Главная
                    </a>
                </li>

                <li>
                    <a href="{{ route('home') }}#zones" class="text-[#00FA9A] transition hover:text-[#00FFFF]">
                        Игровые зоны
                    </a>
                </li>

                <li>
                    <a href="{{ route('home') }}#about" class="text-[#00FA9A] transition hover:text-[#00FFFF]">
                        О клубе
                    </a>
                </li>

                <li>
                    <a href="{{ route('home') }}#contacts" class="text-[#00FA9A] transition hover:text-[#00FFFF]">
                        Контакты
                    </a>
                </li>
            </ul>
        </div>

        {{-- ИНФОРМАЦИЯ --}}
        <div>
            <h3 class="mb-5 text-lg font-bold text-[#B7FF00]">
                Информация
            </h3>

            <ul class="space-y-3 text-sm">
                <li>
                    <a href="#" class="text-[#00FA9A] transition hover:text-[#00FFFF]">
                        Правила клуба
                    </a>
                </li>

                <li>
                    <a href="#" class="text-[#00FA9A] transition hover:text-[#00FFFF]">
                        Политика конфиденциальности
                    </a>
                </li>

                <li>
                    <a href="#" class="text-[#00FA9A] transition hover:text-[#00FFFF]">
                        Условия бронирования
                    </a>
                </li>
            </ul>
        </div>

        {{-- КОНТАКТЫ --}}
        <div>
            <h3 class="mb-5 text-lg font-bold text-[#B7FF00]">
                Контакты
            </h3>

            <div class="space-y-3 text-sm text-[#00FA9A]">
                <p>Компьютерный клуб LimeNight</p>
                <p>Казахстан</p>
                <p>Работаем для побед</p>
            </div>

            {{-- Позже добавим реальные телефон, адрес и соцсети --}}
        </div>

    </div>

    {{-- НИЖНЯЯ ПОЛОСА --}}
    <div class="border-t border-[#292929]">

        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-6 py-6 text-center sm:flex-row">

            <p class="text-xs text-[#00FA9A]">
                © {{ date('Y') }} LimeNight. Все права защищены.
            </p>

            <p class="text-xs font-bold tracking-widest text-[#B7FF00]">
                PLAY. WIN. REPEAT.
            </p>

        </div>

    </div>

</footer>
