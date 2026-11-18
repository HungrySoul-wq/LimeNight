@extends('layouts.app')

@section('title', 'Оплата бронирования — LimeNight')

@section('content')

<section class="min-h-screen bg-black px-4 py-12 text-[#00FA9A] sm:px-8">
    <div class="mx-auto max-w-3xl">

        <a href="{{ route('profile') }}"
           class="text-sm text-[#00FFFF] transition hover:text-[#B7FF00]">
            ← Вернуться в личный кабинет
        </a>

        <div class="mb-8 mt-6 text-center">
            <h1 class="text-3xl font-black text-[#B7FF00] sm:text-5xl">
                Оплата бронирования
            </h1>

            <p class="mt-3 text-gray-400">
                Проверь данные брони перед демонстрационной оплатой.
            </p>
        </div>

        {{-- Информация о бронировании --}}
        <div class="rounded-2xl border border-[#292929] bg-[#101010] p-6 sm:p-8">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-[#00FFFF]">
                    Бронирование №{{ $booking->id }}
                </h2>

                <span class="rounded-full border border-[#FFB86B]/40 bg-[#FFB86B]/10 px-4 py-2 text-sm font-bold text-[#FFB86B]">
                    Ожидает оплаты
                </span>
            </div>

            <div class="space-y-4">

                <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                    <span class="text-gray-400">Игровая зона</span>
                    <span class="text-right font-bold text-white">
                        {{ $booking->computer?->zone?->name ?? $booking->room?->zone?->name ?? 'Не указана' }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                    <span class="text-gray-400">Место</span>

                    <span class="text-right font-bold text-white">
                        @if($booking->booking_type === 'room')
                            {{ $booking->room?->name ?? 'Комната Bootcamp' }}
                            (целиком)
                        @else
                            ПК {{ $booking->computer?->pc_number ?? 'Не указан' }}
                        @endif
                    </span>
                </div>

                <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                    <span class="text-gray-400">Начало игры</span>
                    <span class="text-right font-bold text-white">
                        {{ $booking->start_time->format('d.m.Y H:i') }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                    <span class="text-gray-400">Окончание игры</span>
                    <span class="text-right font-bold text-white">
                        {{ $booking->end_time->format('d.m.Y H:i') }}
                    </span>
                </div>

                <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                    <span class="text-gray-400">Способ оплаты</span>
                    <span class="text-right font-bold text-white">
                        Банковская карта
                    </span>
                </div>

            </div>

            {{-- Сумма --}}
            <div class="mt-7 rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 p-5 text-center">
                <p class="text-sm text-gray-400">
                    К оплате
                </p>

                <p class="mt-2 text-4xl font-black text-[#B7FF00]">
                    {{ number_format((float) $payment->amount, 0, ',', ' ') }} ₸
                </p>
            </div>

            {{-- Срок оплаты --}}
            <div class="mt-6 rounded-xl border border-[#FFB86B]/30 bg-[#FFB86B]/5 p-5">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">⏳</span>

                    <div>
                        <p class="font-bold text-[#FFB86B]">
                            Время на оплату — 15 минут
                        </p>

                        <p class="mt-1 text-sm text-gray-400">
                            Оплати бронь до
                            {{ $booking->expires_at?->format('d.m.Y H:i') ?? 'окончания срока оплаты' }}.
                            Если время истечёт, бронь будет отменена.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Демонстрационная оплата --}}
            <div class="mt-7">
                <h3 class="mb-3 text-lg font-bold text-white">
                    Демонстрационная оплата
                </h3>

                <p class="mb-5 text-sm leading-6 text-gray-400">
                    Это учебная версия оплаты. Реальные банковские данные
                    вводить не нужно. Нажатие кнопки ниже имитирует успешную
                    оплату и подтверждает бронирование.
                </p>

                <form action="{{ route('payment.demo', $booking) }}"
                      method="POST">

                    @csrf

                    <button type="submit"
                            class="w-full rounded-xl bg-[#B7FF00] px-6 py-4 text-lg font-black text-black transition hover:bg-[#00FFFF]">
                        Оплатить {{ number_format((float) $payment->amount, 0, ',', ' ') }} ₸
                    </button>

                </form>

                <p class="mt-4 text-center text-xs text-gray-500">
                    После успешной демонстрационной оплаты бронь получит статус «Подтверждена».
                </p>
            </div>

        </div>

    </div>
</section>

@endsection
