@extends('layouts.app')

@section('title', 'Редактировать комнату — LimeNight')

@section('content')
<div class="min-h-screen bg-black px-4 py-10 sm:px-6">
    <div class="mx-auto max-w-3xl">

        {{-- ВОЗВРАТ В АДМИН-ПАНЕЛЬ --}}
        <div class="mb-8">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#00FFFF] transition hover:text-[#B7FF00]">
                ← Назад в админ-панель
            </a>
        </div>

        <div class="mb-8">
            <p class="mb-2 text-sm font-semibold uppercase tracking-widest text-[#B7FF00]">
                Администрирование
            </p>

            <h1 class="text-3xl font-bold text-[#B7FF00] sm:text-4xl">
                Редактировать комнату
            </h1>

            <p class="mt-2 text-[#00FA9A]">
                Измени параметры комнаты «{{ $room->name }}».
            </p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-red-400">
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.rooms.update', $room) }}" method="POST"
              class="space-y-6 rounded-2xl border border-[#292929] bg-[#101010] p-6 sm:p-8">
            @csrf
            @method('PUT')

            <div>
                <label for="zone_id" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                    Игровая зона
                </label>

                <select name="zone_id" id="zone_id" required
                        class="w-full rounded-xl border border-[#292929] bg-[#171717] px-4 py-3 text-white outline-none focus:border-[#B7FF00]">
                    @foreach($zones as $zone)
                        <option value="{{ $zone->id }}"
                            {{ old('zone_id', $room->zone_id) == $zone->id ? 'selected' : '' }}>
                            {{ $zone->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="name" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                    Название комнаты
                </label>

                <input type="text" name="name" id="name"
                       value="{{ old('name', $room->name) }}"
                       maxlength="100" required
                       class="w-full rounded-xl border border-[#292929] bg-[#171717] px-4 py-3 text-white outline-none focus:border-[#B7FF00]">
            </div>

            <div>
                <label for="type" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                    Тип комнаты
                </label>

                <select name="type" id="type" required
                        class="w-full rounded-xl border border-[#292929] bg-[#171717] px-4 py-3 text-white outline-none focus:border-[#B7FF00]">
                    <option value="standard" {{ old('type', $room->type) === 'standard' ? 'selected' : '' }}>
                        Общий зал
                    </option>
                    <option value="vip" {{ old('type', $room->type) === 'vip' ? 'selected' : '' }}>
                        VIP
                    </option>
                    <option value="bootcamp" {{ old('type', $room->type) === 'bootcamp' ? 'selected' : '' }}>
                        Bootcamp
                    </option>
                </select>
            </div>

            <div>
                <label for="capacity" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                    Вместимость комнаты
                </label>

                <input type="number" name="capacity" id="capacity"
                       value="{{ old('capacity', $room->capacity) }}"
                       min="1" max="100" required
                       class="w-full rounded-xl border border-[#292929] bg-[#171717] px-4 py-3 text-white outline-none focus:border-[#B7FF00]">
            </div>

            <div>
                <label for="is_active" class="mb-2 block text-sm font-semibold text-[#00FA9A]">
                    Статус комнаты
                </label>

                <select name="is_active" id="is_active" required
                        class="w-full rounded-xl border border-[#292929] bg-[#171717] px-4 py-3 text-white outline-none focus:border-[#B7FF00]">
                    <option value="1" {{ old('is_active', (string) (int) $room->is_active) === '1' ? 'selected' : '' }}>
                        Активна
                    </option>
                    <option value="0" {{ old('is_active', (string) (int) $room->is_active) === '0' ? 'selected' : '' }}>
                        Неактивна
                    </option>
                </select>
            </div>

            <div class="flex flex-col gap-3 border-t border-[#292929] pt-6 sm:flex-row">
                <button type="submit"
                        class="rounded-xl bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#a5e600]">
                    Сохранить изменения
                </button>

                <a href="{{ route('admin.rooms.index') }}"
                   class="rounded-xl border border-[#292929] px-6 py-3 text-center font-semibold text-[#00FA9A] transition hover:bg-[#171717]">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
