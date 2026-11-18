{{-- =====================================================
     LIMENIGHT — ЛИЧНЫЙ КАБИНЕТ
     Профиль пользователя и его бронирования
===================================================== --}}

@extends('layouts.app')

@section('title', 'Личный кабинет — LimeNight')

@section('content')

<section class="min-h-screen bg-black px-4 py-12 text-[#00FA9A] sm:px-6">

    <div class="mx-auto max-w-6xl">

        {{-- ЗАГОЛОВОК СТРАНИЦЫ --}}
        <div class="mb-10">
            <p class="mb-2 text-sm font-bold uppercase tracking-[0.3em] text-[#00FFFF]">
                LIMENIGHT ACCOUNT
            </p>

            <h1 class="text-3xl font-black text-[#B7FF00] sm:text-4xl">
                Личный кабинет
            </h1>

            <p class="mt-3 text-gray-400">
                Здесь ты можешь посмотреть информацию о своём аккаунте и бронированиях.
            </p>
        </div>

        {{-- СООБЩЕНИЯ --}}
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

        {{-- ПРОФИЛЬ ПОЛЬЗОВАТЕЛЯ --}}
        <div class="mb-8 rounded-2xl border border-[#292929] bg-[#101010] p-6 sm:p-8">

            <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
                <h2 class="text-xl font-bold text-[#B7FF00]">
                    Мой профиль
                </h2>

                <span class="rounded-full border border-[#00FFFF] px-4 py-1.5 text-xs font-bold text-[#00FFFF]">
                    {{ auth()->user()->role === 'client' ? 'Клиент' : auth()->user()->role }}
                </span>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">

                <div>
                    <p class="mb-2 text-sm text-gray-500">Имя пользователя</p>
                    <p class="font-semibold text-[#00FA9A]">
                        {{ auth()->user()->name }}
                    </p>
                </div>

                <div>
                    <p class="mb-2 text-sm text-gray-500">Электронная почта</p>
                    <p class="font-semibold text-[#00FA9A]">
                        {{ auth()->user()->email }}
                    </p>
                </div>

                <div>
                    <p class="mb-2 text-sm text-gray-500">Номер телефона</p>
                    <p class="font-semibold text-[#00FA9A]">
                        {{ auth()->user()->phone ?: 'Не указан' }}
                    </p>
                </div>

                <div>
                    <p class="mb-2 text-sm text-gray-500">Дата регистрации</p>
                    <p class="font-semibold text-[#00FA9A]">
                        {{ auth()->user()->created_at?->format('d.m.Y') ?? '—' }}
                    </p>
                </div>

            </div>
        </div>

        {{-- ИСТОРИЯ БРОНИРОВАНИЙ --}}
        <div class="rounded-2xl border border-[#292929] bg-[#101010] p-6 sm:p-8">

            <div class="mb-6">
                <h2 class="text-xl font-bold text-[#B7FF00]">
                    Мои бронирования
                </h2>

                <p class="mt-2 text-sm text-gray-400">
                    История твоих заказов в компьютерном клубе.
                </p>
            </div>

            @if(isset($bookings) && $bookings->count() > 0)

                <div class="space-y-4">

                    @foreach($bookings as $booking)

                        @php
                            $pendingPayment = $booking->payments
                                ->where('status', 'pending')
                                ->sortByDesc('created_at')
                                ->first();

                            $paidPayment = $booking->payments
                                ->where('status', 'paid')
                                ->sortByDesc('created_at')
                                ->first();

                            $latestPayment = $booking->payments
                                ->sortByDesc('created_at')
                                ->first();
                        @endphp

                        <div class="rounded-xl border border-[#292929] bg-black p-5">

                            <div class="flex flex-wrap items-center justify-between gap-4">

                                <div>
                                    <p class="font-bold text-[#00FA9A]">
                                        Бронирование №{{ $booking->id }}
                                    </p>

                                    <p class="mt-2 text-sm text-gray-400">
                                        Начало:
                                        {{ $booking->start_time?->format('d.m.Y H:i') ?? '—' }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-400">
                                        Окончание:
                                        {{ $booking->end_time?->format('d.m.Y H:i') ?? '—' }}
                                    </p>

                                    @if($booking->booking_type === 'room')
                                        <p class="mt-1 text-sm text-[#00FFFF]">
                                            {{ $booking->room?->name ?? 'Комната Bootcamp' }}
                                            — бронирование комнаты целиком
                                        </p>
                                    @elseif($booking->computer)
                                        <p class="mt-1 text-sm text-[#00FFFF]">
                                            {{ $booking->computer->zone?->name ?? 'Игровая зона' }},
                                            ПК №{{ $booking->computer->pc_number }}
                                        </p>
                                    @endif
                                </div>

                                <div class="text-right">

                                    <p class="text-lg font-black text-[#B7FF00]">
                                        {{ number_format((float) $booking->total_price, 0, ',', ' ') }} ₸
                                    </p>

                                    <span class="mt-2 inline-block rounded-full border border-[#292929] px-3 py-1 text-xs text-[#00FFFF]">
                                        @switch($booking->status)
                                            @case('pending')
                                                Ожидает оплаты
                                                @break
                                            @case('confirmed')
                                                Подтверждено
                                                @break
                                            @case('cancelled')
                                                Отменено
                                                @break
                                            @case('completed')
                                                Завершено
                                                @break
                                            @default
                                                {{ $booking->status }}
                                        @endswitch
                                    </span>

                                </div>

                            </div>

                            {{-- ИНФОРМАЦИЯ О ПЛАТЕЖЕ --}}
                            @if($latestPayment)

                                <div class="mt-5 rounded-xl border border-[#292929] bg-[#101010] p-4">

                                    <p class="mb-3 font-bold text-white">
                                        Информация об оплате
                                    </p>

                                    <div class="flex flex-wrap justify-between gap-2 text-sm">
                                        <span class="text-gray-400">
                                            Способ оплаты
                                        </span>

                                        <span class="font-semibold text-[#00FFFF]">
                                            @switch($latestPayment->payment_method)
                                                @case('card')
                                                    Банковская карта
                                                    @break
                                                @case('onsite')
                                                    Администратору на месте
                                                    @break
                                                @default
                                                    {{ $latestPayment->payment_method }}
                                            @endswitch
                                        </span>
                                    </div>

                                    <div class="mt-3 flex flex-wrap justify-between gap-2 text-sm">
                                        <span class="text-gray-400">
                                            Статус платежа
                                        </span>

                                        <span class="font-bold
                                            @if($paidPayment) text-[#B7FF00]
                                            @elseif($pendingPayment) text-[#FFB86B]
                                            @else text-gray-400
                                            @endif">

                                            @switch($latestPayment->status)
                                                @case('paid')
                                                    Оплачено
                                                    @break
                                                @case('pending')
                                                    Ожидает оплаты
                                                    @break
                                                @case('cancelled')
                                                    Платёж отменён
                                                    @break
                                                @case('expired')
                                                    Срок оплаты истёк
                                                    @break
                                                @default
                                                    {{ $latestPayment->status }}
                                            @endswitch

                                        </span>
                                    </div>

                                </div>

                            @endif

                            {{-- БЛОК ОПЛАТЫ --}}
                            @if($booking->status === 'pending' && $booking->expires_at)

                                <div class="mt-5 rounded-xl border border-[#B7FF00]/30 bg-[#B7FF00]/5 p-4">

                                    @if($booking->expires_at->isFuture())

                                        <p class="font-bold text-[#B7FF00]">
                                            Оплати бронирование в течение 15 минут
                                        </p>

                                        <p class="mt-2 text-sm text-gray-400">
                                            Осталось времени:
                                        </p>

                                        <p
                                            class="booking-countdown mt-1 text-2xl font-black text-[#00FFFF]"
                                            data-expires-at="{{ $booking->expires_at->toIso8601String() }}"
                                        >
                                            --:--
                                        </p>

                                        @if($pendingPayment && $pendingPayment->payment_method === 'card')

                                            <a href="{{ route('payment.show', $booking->id) }}"
                                               class="mt-4 inline-block rounded-xl bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                                                Оплатить {{ number_format((float) $booking->total_price, 0, ',', ' ') }} ₸
                                            </a>

                                        @elseif($pendingPayment && $pendingPayment->payment_method === 'onsite')

                                            <p class="mt-4 text-sm text-[#00FFFF]">
                                                Способ оплаты: администратору на месте.
                                            </p>

                                            <p class="mt-1 text-sm text-gray-400">
                                                Обратись к администратору клуба для внесения оплаты.
                                            </p>

                                        @endif

                                    @else

                                        <p class="font-bold text-red-400">
                                            Время оплаты истекло
                                        </p>

                                        <p class="mt-1 text-sm text-gray-400">
                                            Бронирование ожидает автоматической отмены системой.
                                        </p>

                                    @endif

                                </div>

                            @endif

                            {{-- КНОПКА ОТМЕНЫ БРОНИ --}}
                            @if($booking->status === 'pending' && $booking->start_time && $booking->start_time->isFuture())

                                <div class="mt-5 border-t border-[#292929] pt-4">

                                    <form action="{{ route('bookings.cancel', $booking->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Ты действительно хочешь отменить это бронирование?');">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="rounded-lg border border-red-500/50 px-5 py-2.5 text-sm font-bold text-red-400 transition hover:bg-red-500 hover:text-white">
                                            Отменить бронь
                                        </button>

                                    </form>

                                </div>

                            @endif

                        </div>

                    @endforeach

                </div>

                {{-- ПОСТРАНИЧНЫЙ ВЫВОД --}}
                @if(method_exists($bookings, 'links'))
                    <div class="mt-6">
                        {{ $bookings->links() }}
                    </div>
                @endif

            @else

                <div class="rounded-xl border border-dashed border-[#292929] px-5 py-12 text-center">

                    <h3 class="text-lg font-bold text-[#B7FF00]">
                        Пока нет бронирований
                    </h3>

                    <p class="mt-3 text-sm text-gray-400">
                        Выбери игровую зону и забронируй свой компьютер!
                    </p>

                    <a href="{{ url('/halls') }}"
                       class="mt-6 inline-block rounded-xl bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                        Выбрать игровую зону
                    </a>

                </div>

            @endif

        </div>

    </div>

</section>

{{-- ТАЙМЕР ОБРАТНОГО ОТСЧЁТА --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const timers = document.querySelectorAll('.booking-countdown');

    function updateTimers() {
        timers.forEach(function (timer) {

            const expiration = new Date(timer.dataset.expiresAt).getTime();
            const remaining = expiration - Date.now();

            if (remaining <= 0) {
                timer.textContent = '00:00';
                timer.classList.remove('text-[#00FFFF]');
                timer.classList.add('text-red-400');

                const block = timer.closest('.rounded-xl');

                if (block) {
                    const heading = block.querySelector('p.font-bold');

                    if (heading) {
                        heading.textContent = 'Время оплаты истекло';
                        heading.classList.remove('text-[#B7FF00]');
                        heading.classList.add('text-red-400');
                    }
                }

                return;
            }

            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);

            timer.textContent =
                String(minutes).padStart(2, '0') + ':' +
                String(seconds).padStart(2, '0');
        });
    }

    updateTimers();
    setInterval(updateTimers, 1000);

});
</script>

@endsection
