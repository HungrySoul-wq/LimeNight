<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Название текущей страницы --}}
    <title>@yield('title', 'LimeNight — компьютерный клуб')</title>

    {{-- Описание сайта для поисковых систем --}}
    <meta name="description" content="LimeNight — современный компьютерный клуб. Выбирай зону, бронируй компьютер и играй!">

    {{-- Подключение CSS и JavaScript через Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-night text-[#00FA9A]">

    {{-- Верхняя часть сайта: логотип и навигация --}}
    @include('components.header')

    {{-- Основное содержимое каждой страницы --}}
    <main>
        @yield('content')
    </main>

    {{-- Нижняя часть сайта --}}
    @include('components.footer')

</body>
</html>
