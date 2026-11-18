@extends('layouts.app')

@section('title', 'Управление бронированиями — LimeNight')

@section('content')
<section class="min-h-screen bg-black px-4 py-12 text-[#00FA9A] sm:px-6">
    <div class="mx-auto max-w-7xl">

        {{-- ВОЗВРАТ В АДМИН-ПАНЕЛЬ --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 font-bold text-[#B7FF00] transition hover:text-[#00FFFF] hover:drop-shadow-[0_0_8px_#B7FF00]">
                <span class="text-xl">←</span>
                Вернуться в админ-панель
            </a>
        </div>

        <div class="mb-10">
            <p class="mb-2 text-sm font-bold uppercase tracking-[0.3em] text-[#00FFFF]">
                LIMENIGHT ADMIN PANEL
            </p>

            <h1 class="text-3xl font-black text-[#B7FF00] sm:text-4xl">
                Управление бронированиями
            </h1>

            <p class="mt-3 text-gray-400">
                Просмотр бронирований, подтверждение оплаты, отмена и удаление тестовых записей.
            </p>
        </div>

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

        <div class="overflow-hidden rounded-2xl border border-[#292929] bg-[#101010]">

            <div class="border-b border-[#292929] p-6">
                <h2 class="text-xl font-bold text-[#B7FF00]">
                    Все бронирования
                </h2>

                <p class="mt-2 text-sm text-gray-400">
                    На этой странице: {{ $bookings->count() }}
                </p>
            </div>

            @if($bookings->count() > 0)
                <div class="space-y-4 p-4 sm:p-6">

                    @foreach($bookings as $booking)
                        @php
                            $payment = $booking->payments
                                ->where('status', 'pending')
                                ->sortByDesc('created_at')
                                ->first();

                            $paidPayment = $booking->payments
                                ->where('status', 'paid')
                                ->sortByDesc('created_at')
                                ->first();

                            $currentPayment = $paidPayment ?? $payment;
                        @endphp

                        <div class="rounded-xl border border-[#292929] bg-black p-5">

                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-bold text-[#00FA9A]">
                                        Бронирование №{{ $booking->id }}
                                    </h3>

                                    <p class="mt-2 text-sm text-gray-400">
                                        Клиент: {{ $booking->user?->name ?? 'Пользователь удалён' }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-400">
                                        Email: {{ $booking->user?->email ?? '—' }}
                                    </p>

                                    <p class="mt-1 text-sm text-gray-400">
                                        Телефон: {{ $booking->user?->phone ?? '—' }}
                                    </p>
                                </div>

                                <div class="text-left sm:text-right">
                                    <p class="text-xl font-black text-[#B7FF00]">
                                        {{ number_format((float) $booking->total_price, 0, ',', ' ') }} ₸
                                    </p>

                                    <span class="mt-2 inline-block rounded-full border border-[#292929] px-3 py-1 text-xs
                                        {{ $booking->status === 'confirmed' ? 'border-[#B7FF00]/50 text-[#B7FF00]' : '' }}
                                        {{ $booking->status === 'pending' ? 'border-[#FFB86B]/50 text-[#FFB86B]' : '' }}
                                        {{ $booking->status === 'cancelled' ? 'border-red-500/50 text-red-400' : '' }}
                                        {{ $booking->status === 'completed' ? 'border-gray-500 text-gray-400' : '' }}
                                        {{ $booking->status === 'active' ? 'border-[#00FFFF]/50 text-[#00FFFF]' : '' }}">
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
                                            @case('active')
                                                Активно
                                                @break
                                            @default
                                                {{ $booking->status }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>

                            <div class="mt-5 grid gap-4 border-t border-[#292929] pt-4 sm:grid-cols-2 lg:grid-cols-3">

                                <div>
                                    <p class="text-xs text-gray-500">Начало</p>
                                    <p class="mt-1 text-sm text-[#00FA9A]">
                                        {{ $booking->start_time?->format('d.m.Y H:i') ?? '—' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Окончание</p>
                                    <p class="mt-1 text-sm text-[#00FA9A]">
                                        {{ $booking->end_time?->format('d.m.Y H:i') ?? '—' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Место</p>
                                    <p class="mt-1 text-sm text-[#00FA9A]">
                                        @if($booking->booking_type === 'room')
                                            {{ $booking->room?->zone?->name ?? 'Комната' }}
                                            — {{ $booking->room?->name ?? '—' }}
                                        @elseif($booking->computer)
                                            {{ $booking->computer->zone?->name ?? 'Игровая зона' }}
                                            — ПК №{{ $booking->computer->pc_number }}
                                        @else
                                            Не указано
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Способ оплаты</p>
                                    <p class="mt-1 text-sm text-[#00FFFF]">
                                        @if($currentPayment?->payment_method === 'card')
                                            Банковская карта
                                        @elseif($currentPayment?->payment_method === 'onsite')
                                            Администратору на месте
                                        @else
                                            Не указан
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Статус платежа</p>
                                    <p class="mt-1 text-sm text-[#00FA9A]">
                                        @if($paidPayment)
                                            Оплачено
                                        @elseif($payment)
                                            Ожидает оплаты
                                        @else
                                            Платёж не найден
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Срок оплаты</p>
                                    <p class="mt-1 text-sm text-[#00FA9A]">
                                        {{ $booking->expires_at?->format('d.m.Y H:i:s') ?? '—' }}
                                    </p>
                                </div>

                            </div>

                            {{-- ПОДТВЕРЖДЕНИЕ ОПЛАТЫ --}}
                            @if(
                                $booking->status === 'pending' &&
                                $payment &&
                                $payment->payment_method === 'onsite' &&
                                $booking->expires_at &&
                                $booking->expires_at->isFuture()
                            )
                                <div class="mt-5 border-t border-[#292929] pt-4">
                                    <form action="{{ route('admin.bookings.confirm-payment', $booking->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Подтвердить получение оплаты за бронирование №{{ $booking->id }}?');">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="rounded-xl bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                                            ✓ Подтвердить получение оплаты
                                        </button>
                                    </form>
                                </div>
                            @elseif($booking->status === 'pending' && $payment && $payment->payment_method === 'card')
                                <p class="mt-5 border-t border-[#292929] pt-4 text-sm text-[#00FFFF]">
                                    Клиенту необходимо завершить оплату банковской картой.
                                </p>
                            @endif

                            {{-- КНОПКИ УПРАВЛЕНИЯ --}}
                            @if(in_array($booking->status, ['pending', 'confirmed', 'active'], true))
                                <div class="mt-5 flex flex-wrap justify-end gap-3 border-t border-[#292929] pt-4">

                                    <form action="{{ route('admin.bookings.cancel', $booking->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Отменить бронирование №{{ $booking->id }}?');">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="rounded-xl border border-[#FFB86B]/50 bg-[#FFB86B]/10 px-5 py-2.5 text-sm font-bold text-[#FFB86B] transition hover:bg-[#FFB86B]/20">
                                            Отменить бронирование
                                        </button>
                                    </form>

                                </div>
                            @endif

                            {{-- УДАЛЕНИЕ ТОЛЬКО ОТМЕНЁННОЙ БРОНИ --}}
                            @if($booking->status === 'cancelled')
                                <div class="mt-5 flex justify-end border-t border-[#292929] pt-4">

                                    <form action="{{ route('admin.bookings.destroy', $booking->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Безвозвратно удалить отменённое бронирование №{{ $booking->id }} и связанные с ним неоплаченные платежи?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="rounded-xl border border-red-500/40 bg-red-500/10 px-5 py-2.5 text-sm font-bold text-red-400 transition hover:bg-red-500/20">
                                            Удалить бронирование
                                        </button>
                                    </form>

                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>

                <div class="border-t border-[#292929] p-5">
                    {{ $bookings->links() }}
                </div>
            @else
                <div class="px-5 py-12 text-center">
                    <h3 class="text-lg font-bold text-[#B7FF00]">
                        Бронирований пока нет
                    </h3>

                    <p class="mt-2 text-sm text-gray-400">
                        Новые бронирования появятся здесь автоматически.
                    </p>
                </div>
            @endif

        </div>
    </div>
</section>
@endsection
