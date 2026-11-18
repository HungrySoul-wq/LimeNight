@extends('layouts.app')

@section('title', 'LimeNight — компьютерный клуб')

@section('content')

{{-- =====================================================
     ГЛАВНАЯ СТРАНИЦА LIMENIGHT
===================================================== --}}

{{-- ГЛАВНЫЙ ПРИВЕТСТВЕННЫЙ ЭКРАН --}}
<section class="relative overflow-hidden border-b border-[#292929]">

    {{-- Декоративное свечение на фоне --}}
    <div class="pointer-events-none absolute -right-40 -top-40 h-96 w-96 rounded-full bg-[#B7FF00]/10 blur-[120px]"></div>
    <div class="pointer-events-none absolute -bottom-40 -left-40 h-96 w-96 rounded-full bg-[#00FFFF]/10 blur-[120px]"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-6 py-24 lg:grid-cols-2 lg:py-36">

        {{-- ТЕКСТОВАЯ ЧАСТЬ --}}
        <div>

            <div class="mb-7 inline-flex rounded-full border border-[#B7FF00]/40 bg-[#B7FF00]/5 px-5 py-2">
                <span class="text-xs font-bold uppercase tracking-[3px] text-[#B7FF00]">
                    Computer Club • Kazakhstan
                </span>
            </div>

            <h1 class="text-5xl font-black uppercase leading-tight tracking-tight sm:text-6xl lg:text-7xl">
                ТВОЯ ИГРА.
                <span class="text-[#B7FF00]">ТВОИ ПРАВИЛА.</span>
                <span class="text-[#00FFFF]">ТВОЙ LIMENIGHT.</span>
            </h1>

            <p class="mt-8 max-w-xl text-base leading-8 text-[#00FA9A] sm:text-lg">
                Погрузись в атмосферу настоящего киберспорта.
                Мощные игровые компьютеры, комфортные зоны,
                командные игры и незабываемые победы.
            </p>

            {{-- КНОПКИ --}}
            <div class="mt-10 flex flex-wrap gap-4">

                <a href="{{ url('/halls') }}" class="btn-lime">
                    Выбрать игровую зону
                </a>

                <a href="#about" class="btn-aqua">
                    О нашем клубе
                </a>

            </div>

        </div>

        {{-- ПРАВАЯ ЧАСТЬ — ИНФОРМАЦИОННАЯ КАРТОЧКА --}}
        <div class="relative">

            <div class="zone-card lime-glow p-8 sm:p-10">

                <div class="mb-8 flex items-center justify-between">

                    <span class="text-sm font-bold uppercase tracking-widest text-[#00FA9A]">
                        LIMENIGHT ARENA
                    </span>

                    <span class="rounded-full border border-[#B7FF00]/40 px-3 py-1 text-xs font-bold text-[#B7FF00]">
                        GAMING
                    </span>

                </div>

                <div class="text-center">

                    <div class="text-7xl font-black text-[#B7FF00] text-glow sm:text-8xl">
                        63
                    </div>

                    <p class="mt-3 text-sm font-bold uppercase tracking-[4px] text-[#00FFFF]">
                        Игровых компьютера
                    </p>

                </div>

                <div class="mt-10 grid grid-cols-3 gap-3">

                    <div class="rounded-xl border border-[#292929] bg-black p-4 text-center">
                        <div class="text-2xl font-black text-[#B7FF00]">48</div>
                        <div class="mt-1 text-xs text-[#00FA9A]">Общий зал</div>
                    </div>

                    <div class="rounded-xl border border-[#292929] bg-black p-4 text-center">
                        <div class="text-2xl font-black text-[#00FFFF]">10</div>
                        <div class="mt-1 text-xs text-[#00FA9A]">VIP</div>
                    </div>

                    <div class="rounded-xl border border-[#292929] bg-black p-4 text-center">
                        <div class="text-2xl font-black text-[#B7FF00]">5</div>
                        <div class="mt-1 text-xs text-[#00FA9A]">Bootcamp</div>
                    </div>

                </div>

                <div class="mt-8 border-t border-[#292929] pt-6 text-center">
                    <p class="text-sm font-bold tracking-widest text-[#00FA9A]">
                        PLAY. WIN. REPEAT.
                    </p>
                </div>

            </div>

        </div>

    </div>

</section>


{{-- =====================================================
     ИГРОВЫЕ ЗОНЫ
===================================================== --}}

<section id="zones" class="mx-auto max-w-7xl scroll-mt-28 px-6 py-24">

    <div class="mb-14 text-center">

        <p class="mb-4 text-sm font-bold uppercase tracking-[4px] text-[#00FFFF]">
            Choose your experience
        </p>

        <h2 class="text-4xl font-black uppercase text-[#B7FF00] sm:text-5xl">
            ИГРОВЫЕ ЗОНЫ
        </h2>

        <p class="mx-auto mt-5 max-w-2xl leading-7 text-[#00FA9A]">
            Выбери подходящую атмосферу для своей игры.
            У нас есть места для одиночных игроков и целых команд.
        </p>

    </div>

    {{-- КАРТОЧКИ ТРЁХ ЗОН --}}
    <div class="grid grid-cols-1 gap-7 md:grid-cols-2 xl:grid-cols-3">

        {{-- ОБЩИЙ ЗАЛ --}}
        <article class="zone-card flex flex-col overflow-hidden">

            <div class="border-b border-[#292929] bg-[#151515] p-8">

                <div class="mb-5 flex items-center justify-between">

                    <span class="rounded-lg bg-[#B7FF00]/10 px-3 py-2 text-xs font-bold text-[#B7FF00]">
                        STANDARD
                    </span>

                    <span class="text-3xl font-black text-[#B7FF00]">
                        &#9632;
                    </span>

                </div>

                <h3 class="text-2xl font-black uppercase text-[#B7FF00]">
                    Общий зал
                </h3>

                <p class="mt-3 text-sm leading-6 text-[#00FA9A]">
                    Комфортная игровая атмосфера для друзей,
                    одиночных игроков и ежедневных побед.
                </p>

            </div>

            <div class="flex flex-1 flex-col p-8">

                <div class="mb-6 space-y-3 text-sm text-[#00FA9A]">

                    <div class="flex justify-between">
                        <span>Компьютеров</span>
                        <strong class="text-[#B7FF00]">48</strong>
                    </div>

                    <div class="flex justify-between">
                        <span>Комнат</span>
                        <strong class="text-[#B7FF00]">3</strong>
                    </div>

                    <div class="flex justify-between">
                        <span>Мест в комнате</span>
                        <strong class="text-[#B7FF00]">16</strong>
                    </div>

                </div>

                <div class="mb-7 mt-auto border-t border-[#292929] pt-5">

                    <p class="mb-3 text-xs font-bold uppercase tracking-widest text-[#00FFFF]">
                        Стоимость
                    </p>

                    <div class="space-y-2 text-sm text-[#00FA9A]">

                        <div class="flex justify-between">
                            <span>1 час</span>
                            <strong>500 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>3 часа</span>
                            <strong>1 300 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>5 часов</span>
                            <strong>2 000 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Ночь</span>
                            <strong>3 500 &#8376;</strong>
                        </div>

                    </div>

                </div>

                {{-- ПЕРЕХОД К ИГРОВЫМ ЗОНАМ --}}
                <a href="{{ url('/halls') }}"
                   class="btn-lime block w-full text-center">
                    Выбрать общий зал
                </a>

            </div>

        </article>


        {{-- VIP --}}
        <article class="zone-card flex flex-col overflow-hidden">

            <div class="border-b border-[#292929] bg-[#151515] p-8">

                <div class="mb-5 flex items-center justify-between">

                    <span class="rounded-lg bg-[#00FFFF]/10 px-3 py-2 text-xs font-bold text-[#00FFFF]">
                        PREMIUM
                    </span>

                    <span class="text-3xl font-black text-[#00FFFF]">
                        &#9670;
                    </span>

                </div>

                <h3 class="text-2xl font-black uppercase text-[#00FFFF]">
                    VIP-зона
                </h3>

                <p class="mt-3 text-sm leading-6 text-[#00FA9A]">
                    Больше приватности, отдельное пространство
                    и комфорт для тех, кто выбирает особенную атмосферу.
                </p>

            </div>

            <div class="flex flex-1 flex-col p-8">

                <div class="mb-6 space-y-3 text-sm text-[#00FA9A]">

                    <div class="flex justify-between">
                        <span>Компьютеров</span>
                        <strong class="text-[#00FFFF]">10</strong>
                    </div>

                    <div class="flex justify-between">
                        <span>Комнат</span>
                        <strong class="text-[#00FFFF]">1</strong>
                    </div>

                    <div class="flex justify-between">
                        <span>Мест в комнате</span>
                        <strong class="text-[#00FFFF]">10</strong>
                    </div>

                </div>

                <div class="mb-7 mt-auto border-t border-[#292929] pt-5">

                    <p class="mb-3 text-xs font-bold uppercase tracking-widest text-[#00FFFF]">
                        Стоимость
                    </p>

                    <div class="space-y-2 text-sm text-[#00FA9A]">

                        <div class="flex justify-between">
                            <span>1 час</span>
                            <strong>750 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>3 часа</span>
                            <strong>1 950 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>5 часов</span>
                            <strong>3 000 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Ночь</span>
                            <strong>5 250 &#8376;</strong>
                        </div>

                    </div>

                </div>

                {{-- ПЕРЕХОД К ИГРОВЫМ ЗОНАМ --}}
                <a href="{{ url('/halls') }}"
                   class="btn-aqua block w-full text-center">
                    Выбрать VIP
                </a>

            </div>

        </article>


        {{-- BOOTCAMP --}}
        <article class="zone-card flex flex-col overflow-hidden">

            <div class="border-b border-[#292929] bg-[#151515] p-8">

                <div class="mb-5 flex items-center justify-between">

                    <span class="rounded-lg bg-[#B7FF00]/10 px-3 py-2 text-xs font-bold text-[#B7FF00]">
                        TEAM ZONE
                    </span>

                    <span class="text-3xl font-black text-[#B7FF00]">
                        &#9733;
                    </span>

                </div>

                <h3 class="text-2xl font-black uppercase text-[#B7FF00]">
                    Bootcamp
                </h3>

                <p class="mt-3 text-sm leading-6 text-[#00FA9A]">
                    Отдельная комната для команды из пяти человек.
                    Тренируйтесь вместе и готовьтесь к победам.
                </p>

            </div>

            <div class="flex flex-1 flex-col p-8">

                <div class="mb-6 space-y-3 text-sm text-[#00FA9A]">

                    <div class="flex justify-between">
                        <span>Компьютеров</span>
                        <strong class="text-[#B7FF00]">5</strong>
                    </div>

                    <div class="flex justify-between">
                        <span>Комнат</span>
                        <strong class="text-[#B7FF00]">1</strong>
                    </div>

                    <div class="flex justify-between">
                        <span>Размер команды</span>
                        <strong class="text-[#B7FF00]">5 игроков</strong>
                    </div>

                </div>

                <div class="mb-7 mt-auto border-t border-[#292929] pt-5">

                    <p class="mb-2 text-xs font-bold uppercase tracking-widest text-[#00FFFF]">
                        Стоимость всей комнаты
                    </p>

                    <p class="mb-3 text-xs text-[#00FA9A]">
                        Одна оплата за команду, а не за каждого игрока.
                    </p>

                    <div class="space-y-2 text-sm text-[#00FA9A]">

                        <div class="flex justify-between">
                            <span>1 час</span>
                            <strong>2 000 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>3 часа</span>
                            <strong>5 000 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>5 часов</span>
                            <strong>7 500 &#8376;</strong>
                        </div>

                        <div class="flex justify-between">
                            <span>Ночь</span>
                            <strong>12 500 &#8376;</strong>
                        </div>

                    </div>

                </div>

                {{-- ПЕРЕХОД К ИГРОВЫМ ЗОНАМ --}}
                <a href="{{ url('/halls') }}"
                   class="btn-lime block w-full text-center">
                    Выбрать Bootcamp
                </a>

            </div>

        </article>

    </div>

</section>


{{-- =====================================================
     О КЛУБЕ
===================================================== --}}

<section id="about" class="scroll-mt-28 border-y border-[#292929] bg-[#080808]">

    <div class="mx-auto grid max-w-7xl items-center gap-12 px-6 py-24 lg:grid-cols-2">

        <div>

            <p class="mb-4 text-sm font-bold uppercase tracking-[4px] text-[#00FFFF]">
                About LimeNight
            </p>

            <h2 class="text-4xl font-black uppercase text-[#B7FF00] sm:text-5xl">
                БОЛЬШЕ ЧЕМ ПРОСТО ИГРА
            </h2>

            <p class="mt-7 leading-8 text-[#00FA9A]">
                LimeNight — место, где встречаются геймеры,
                собираются команды и рождаются новые победы.
                Мы создаём пространство для комфортной игры,
                общения и киберспортивного развития.
            </p>

            <p class="mt-5 leading-8 text-[#00FA9A]">
                Выбирай подходящую зону, бронируй компьютер
                и наслаждайся игровым процессом.
            </p>

            <a href="{{ url('/halls') }}" class="btn-lime mt-8">
                Выбрать зону
            </a>

        </div>

        {{-- ИНФОРМАЦИЯ О ВОЗМОЖНОСТЯХ --}}
        <div class="grid grid-cols-2 gap-4">

            <div class="zone-card p-6">
                <div class="text-3xl font-black text-[#B7FF00]">63</div>
                <p class="mt-2 text-sm text-[#00FA9A]">Игровых компьютера</p>
            </div>

            <div class="zone-card p-6">
                <div class="text-3xl font-black text-[#00FFFF]">3</div>
                <p class="mt-2 text-sm text-[#00FA9A]">Игровые зоны</p>
            </div>

            <div class="zone-card p-6">
                <div class="text-3xl font-black text-[#00FFFF]">5</div>
                <p class="mt-2 text-sm text-[#00FA9A]">Мест в Bootcamp</p>
            </div>

            <div class="zone-card p-6">
                <div class="text-3xl font-black text-[#B7FF00]">24/7</div>
                <p class="mt-2 text-sm text-[#00FA9A]">Игровая атмосфера</p>
            </div>

        </div>

    </div>

</section>


{{-- =====================================================
     КОНТАКТЫ
===================================================== --}}

<section id="contacts" class="mx-auto max-w-7xl scroll-mt-28 px-6 py-24">

    <div class="zone-card p-8 text-center sm:p-12">

        <p class="mb-4 text-sm font-bold uppercase tracking-[4px] text-[#00FFFF]">
            Join LimeNight
        </p>

        <h2 class="text-3xl font-black uppercase text-[#B7FF00] sm:text-5xl">
            ГОТОВ К ПОБЕДЕ?
        </h2>

        <p class="mx-auto mt-6 max-w-xl leading-7 text-[#00FA9A]">
            Выбирай игровую зону и бронируй своё место.
            Твоя следующая победа начинается здесь.
        </p>

        <a href="{{ url('/halls') }}" class="btn-lime mt-8">
            Забронировать компьютер
        </a>

    </div>

</section>

@endsection
