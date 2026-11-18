@extends('layouts.app')

@section('title', 'Добавить компьютер — LimeNight')

@section('content')
@php
    $selectedZoneId = old('zone_id', request('zone_id', $selectedRoom?->zone_id));
    $selectedRoomId = old('room_id', request('room_id', $selectedRoom?->id));

    $roomsByZone = $zones->mapWithKeys(function ($zone) {
        return [
            $zone->id => $zone->rooms->map(function ($room) {
                return [
                    'id' => $room->id,
                    'name' => $room->name,
                ];
            })->values(),
        ];
    });
@endphp

<section class="min-h-screen bg-black px-4 py-10 text-[#00FA9A]">
    <div class="mx-auto max-w-3xl">

        <div class="mb-6">
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-[#00FFFF] transition hover:text-[#B7FF00]">
                ← Назад в админ-панель
            </a>
        </div>

        <div class="rounded-2xl border border-[#292929] bg-[#101010] p-6 shadow-xl sm:p-8">
            <h1 class="mb-2 text-3xl font-bold text-[#B7FF00]">
                Добавить компьютер
            </h1>

            <p class="mb-8 text-sm text-gray-400">
                Заполните информацию о новом компьютере.
            </p>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-500/40 bg-red-500/10 p-4 text-red-400">
                    <p class="mb-2 font-semibold">Проверьте введённые данные:</p>
                    <ul class="list-inside list-disc space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.computers.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="zone_id" class="mb-2 block font-semibold">
                        Игровая зона *
                    </label>

                    <select
                        name="zone_id"
                        id="zone_id"
                        required
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none focus:border-[#B7FF00]"
                    >
                        <option value="">Выберите игровую зону</option>

                        @foreach ($zones as $zone)
                            <option value="{{ $zone->id }}"
                                @selected((string) $selectedZoneId === (string) $zone->id)>
                                {{ $zone->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('zone_id')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="room_id" class="mb-2 block font-semibold">
                        Комната *
                    </label>

                    <select
                        name="room_id"
                        id="room_id"
                        required
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none focus:border-[#B7FF00]"
                    >
                        <option value="">Сначала выберите игровую зону</option>
                    </select>

                    @error('room_id')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pc_number" class="mb-2 block font-semibold">
                        Номер компьютера *
                    </label>

                    <input
                        type="number"
                        name="pc_number"
                        id="pc_number"
                        min="1"
                        max="65535"
                        value="{{ old('pc_number') }}"
                        required
                        placeholder="Например: 1"
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none focus:border-[#B7FF00]"
                    >

                    @error('pc_number')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ip_address" class="mb-2 block font-semibold">
                        IP-адрес
                    </label>

                    <input
                        type="text"
                        name="ip_address"
                        id="ip_address"
                        value="{{ old('ip_address') }}"
                        placeholder="Например: 192.168.1.10"
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none focus:border-[#B7FF00]"
                    >

                    @error('ip_address')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="mb-2 block font-semibold">
                        Статус компьютера *
                    </label>

                    <select
                        name="status"
                        id="status"
                        required
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none focus:border-[#B7FF00]"
                    >
                        <option value="available" @selected(old('status', 'available') === 'available')>
                            Свободен
                        </option>
                        <option value="occupied" @selected(old('status') === 'occupied')>
                            Занят
                        </option>
                        <option value="reserved" @selected(old('status') === 'reserved')>
                            Забронирован
                        </option>
                        <option value="maintenance" @selected(old('status') === 'maintenance')>
                            На обслуживании
                        </option>
                    </select>

                    @error('status')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="specs" class="mb-2 block font-semibold">
                        Характеристики компьютера
                    </label>

                    <textarea
                        name="specs"
                        id="specs"
                        rows="4"
                        placeholder="Например: RTX 4070, Intel Core i7, 32 GB RAM"
                        class="w-full rounded-xl border border-[#292929] bg-black px-4 py-3 text-white outline-none focus:border-[#B7FF00]"
                    >{{ old('specs') }}</textarea>

                    @error('specs')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 pt-3 sm:flex-row">
                    <button
                        type="submit"
                        class="rounded-xl bg-[#B7FF00] px-6 py-3 font-bold text-black transition hover:bg-[#a4e600]"
                    >
                        + Добавить компьютер
                    </button>

                    <a
                        href="{{ route('admin.computers.index') }}"
                        class="rounded-xl border border-[#292929] px-6 py-3 text-center text-white transition hover:bg-[#171717]"
                    >
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const zoneSelect = document.getElementById('zone_id');
    const roomSelect = document.getElementById('room_id');

    const roomsByZone = @json($roomsByZone);
    const initialRoomId = @json((string) $selectedRoomId);

    function updateRooms(selectedRoomId = '') {
        const zoneId = zoneSelect.value;

        roomSelect.innerHTML = '';

        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = zoneId
            ? 'Выберите комнату'
            : 'Сначала выберите игровую зону';

        roomSelect.appendChild(placeholder);

        if (!zoneId || !roomsByZone[zoneId]) {
            return;
        }

        roomsByZone[zoneId].forEach(function (room) {
            const option = document.createElement('option');

            option.value = room.id;
            option.textContent = room.name;

            if (String(room.id) === String(selectedRoomId)) {
                option.selected = true;
            }

            roomSelect.appendChild(option);
        });
    }

    zoneSelect.addEventListener('change', function () {
        updateRooms();
    });

    updateRooms(initialRoomId);
});
</script>
@endsection
