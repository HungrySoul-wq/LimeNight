@extends('layouts.app')

@section('title', 'Бронирование создано — LimeNight')

@section('content')

<section class="min-h-screen bg-black px-4 py-16 text-[#00FA9A]">

    <div class="mx-auto max-w-2xl">

        <div class="rounded-2xl border border-[#292929] bg-[#101010] p-8 text-center sm:p-12">

            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full border-2 border-[#B7FF00] bg-[#B7FF00]/10 text-4xl text-[#B7FF00]">
                ✓
            </div>

            <p class="mt-7 text-sm font-bold uppercase tracking-[0.3em] text-[#00FFFF]">
                LIMENIGHT BOOKING
            </p>

            <h1 class="mt-4 text-3xl font-black text-[#B7FF00] sm:text-4xl">
                Бронирование создано!
            </h1>

            <p class="mt-4 leading-7 text-gray-400">
                Твоя заявка успешно создана. Проверь информацию о бронировании
                и заверши оплату в течение 15 минут.
            </p>

            @isset($booking)

                <div class="mt-8 rounded-xl border border-[#292929] bg-black p-6 text-left">

                    <h2 class="mb-5 text-lg font-bold text-[#00FFFF]">
                        Бронирование №{{ $booking->id }}
                    </h2>

                    <div class="space-y-4">

                        <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                            <span class="text-sm text-gray-400">Игровая зона</span>
                            <span class="text-right font-semibold text-white">
                                {{ $booking->computer?->zone?->name ?? $booking->room?->zone?->name ?? 'Не указана' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                            <span class="text-sm text-gray-400">Место</span>
                            <span class="text-right font-semibold text-white">
                                @if($booking->booking_type === 'room')
                                    {{ $booking->room?->name ?? 'Комната Bootcamp' }} (целиком)
                                @else
                                    ПК №{{ $booking->computer?->pc_number ?? 'Не указан' }}
                                @endif
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                            <span class="text-sm text-gray-400">Начало игры</span>
                            <span class="text-right font-semibold text-white">
                                {{ $booking->start_time?->format('d.m.Y H:i') ?? '—' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4 border-b border-[#292929] pb-3">
                            <span class="text-sm text-gray-400">Окончание игры</span>
                            <span class="text-right font-semibold text-white">
                                {{ $booking->end_time?->format('d.m.Y H:i') ?? '—' }}
                            </span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span class="text-sm text-gray-400">Стоимость</span>
                            <span class="text-xl font-black text-[#B7FF00]">
                                {{ number_format((float) $booking->total_price, 0, ',', ' ') }} ₸
                            </span>
                        </div>

                    </div>

                    @if($booking->status === 'pending' && $booking->expires_at)
                        <div class="mt-6 rounded-xl border border-[#FFB86B]/30 bg-[#FFB86B]/5 p-4">
                            <p class="font-bold text-[#FFB86B]">
                                Ожидает оплаты
                            </p>
                            <p class="mt-2 text-sm text-gray-400">
                                Оплати бронирование до
                                {{ $booking->expires_at->format('d.m.Y H:i') }}.
                            </p>
                        </div>
                    @elseif($booking->status === 'confirmed')
                        <div class="mt-6 rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 p-4">
                            <p class="font-bold text-[#B7FF00]">
                                Бронирование подтверждено
                            </p>
                        </div>
                    @endif

                </div>

            @endisset

            <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">

                <a href="{{ route('profile') }}"
                   class="rounded-xl bg-[#B7FF00] px-7 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                    Личный кабинет
                </a>

                <a href="{{ route('halls.index') }}"
                   class="rounded-xl border border-[#00FFFF] px-7 py-3 font-bold text-[#00FFFF] transition hover:bg-[#00FFFF] hover:text-black">
                    Игровые зоны
                </a>

            </div>

        </div>

    </div>

</section>

@endsection
