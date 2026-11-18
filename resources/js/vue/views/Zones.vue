<template>
    <main class="max-w-7xl mx-auto px-6 py-16">
        <p class="text-[#B7FF00] tracking-widest mb-3">
            LIMENIGHT
        </p>

        <h1 class="text-4xl font-bold mb-4">
            Игровые зоны
        </h1>

        <p class="text-gray-400 mb-10">
            Выбери подходящую игровую зону для своей игры.
        </p>

        <div v-if="loading" class="text-[#00FA9A]">
            Загружаем игровые зоны и компьютеры...
        </div>

        <div
            v-else-if="error"
            class="bg-[#101010] border border-red-900 rounded-xl p-6"
        >
            <p class="text-red-400">{{ error }}</p>

            <button
                @click="loadZones"
                class="mt-4 bg-[#B7FF00] text-black px-5 py-2 rounded-lg font-bold"
            >
                Повторить
            </button>
        </div>

        <div v-else-if="zones.length === 0" class="text-gray-400">
            Игровые зоны пока не добавлены.
        </div>

        <div v-else class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <article
                v-for="zone in zones"
                :key="zone.id"
                class="bg-[#101010] border border-[#292929] rounded-xl p-8 hover:border-[#B7FF00] transition flex flex-col"
            >
                <div class="flex-1">
                    <p class="text-xs tracking-widest text-gray-500 mb-3">
                        ИГРОВАЯ ЗОНА
                    </p>

                    <h2 class="text-2xl font-bold text-[#B7FF00] mb-4">
                        {{ displayZoneName(zone.name) }}
                    </h2>

                    <p class="text-gray-400 mb-6 min-h-12">
                        {{ zone.description || 'Игровая зона LimeNight' }}
                    </p>

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-[#181818] rounded-lg p-3">
                            <p class="text-gray-500 text-xs mb-1">
                                Компьютеры
                            </p>
                            <p class="text-white font-bold text-lg">
                                {{ zoneComputers(zone).length }}
                            </p>
                        </div>

                        <div class="bg-[#181818] rounded-lg p-3">
                            <p class="text-gray-500 text-xs mb-1">
                                Комнаты
                            </p>
                            <p class="text-white font-bold text-lg">
                                {{ zoneRooms(zone).length }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-[#292929] pt-5">
                        <p class="text-white font-bold mb-4">
                            Состояние компьютеров
                        </p>

                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-[#172500] rounded-lg p-3 text-center">
                                <p class="text-[#B7FF00] text-xl font-bold">
                                    {{ countStatus(zone, 'available') }}
                                </p>
                                <p class="text-gray-400 text-xs">Свободны</p>
                            </div>

                            <div class="bg-[#29200e] rounded-lg p-3 text-center">
                                <p class="text-orange-400 text-xl font-bold">
                                    {{ countStatus(zone, 'occupied') }}
                                </p>
                                <p class="text-gray-400 text-xs">Заняты</p>
                            </div>

                            <div class="bg-[#2b1010] rounded-lg p-3 text-center">
                                <p class="text-red-400 text-xl font-bold">
                                    {{ countStatus(zone, 'maintenance') }}
                                </p>
                                <p class="text-gray-400 text-xs">Неисправны</p>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-[#292929] mt-5 pt-5">
                        <p class="text-white font-bold mb-4">
                            Комнаты
                        </p>

                        <div v-if="zoneRooms(zone).length" class="space-y-3">
                            <div
                                v-for="room in zoneRooms(zone)"
                                :key="room.id"
                                class="bg-[#181818] rounded-lg p-3"
                            >
                                <div class="flex justify-between items-center gap-2">
                                    <p class="text-white font-medium">
                                        {{ room.name }}
                                    </p>

                                    <span
                                        class="text-xs"
                                        :class="roomAvailable(room)
                                            ? 'text-[#B7FF00]'
                                            : 'text-orange-400'"
                                    >
                                        {{ roomAvailable(room) ? 'Свободна' : 'Есть занятые ПК' }}
                                    </span>
                                </div>

                                <p class="text-gray-500 text-sm mt-1">
                                    {{ room.computers.length }} компьютеров
                                </p>

                                <div class="flex flex-wrap gap-1.5 mt-3">
                                    <span
                                        v-for="computer in room.computers"
                                        :key="computer.id"
                                        class="text-xs px-2 py-1 rounded"
                                        :class="computerStatusClass(computer.status)"
                                    >
                                        №{{ computer.pc_number }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <p v-else class="text-gray-500 text-sm">
                            Комнат пока нет.
                        </p>
                    </div>

                    <div class="border-t border-[#292929] mt-5 pt-5">
                        <p class="text-gray-500 text-sm mb-1">
                            Стоимость за час
                        </p>

                        <p class="text-[#00FA9A] text-2xl font-bold">
                            {{ formatPrice(zone.hourly_rate) }} ₸
                        </p>
                    </div>
                </div>

                <router-link
                    :to="{
                        path: '/app/bookings',
                        query: { zone: zone.id }
                    }"
                    class="block text-center mt-7 bg-[#B7FF00] text-black px-6 py-3 rounded-lg font-bold hover:bg-[#a5e600] transition"
                >
                    Выбрать зону
                </router-link>
            </article>
        </div>
    </main>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../api';

const zones = ref([]);
const computers = ref([]);
const loading = ref(true);
const error = ref('');

function displayZoneName(name) {
    return name === 'COMMON' ? 'Общий зал' : (name || 'Игровая зона');
}

function formatPrice(price) {
    return Number(price || 0).toLocaleString('ru-RU');
}

function zoneComputers(zone) {
    return computers.value.filter(computer =>
        Number(computer.zone?.id ?? computer.zone_id) === Number(zone.id)
    );
}

function zoneRooms(zone) {
    const groups = new Map();

    zoneComputers(zone).forEach(computer => {
        if (!computer.room) return;

        const room = computer.room;

        if (!groups.has(room.id)) {
            groups.set(room.id, {
                ...room,
                computers: [],
            });
        }

        groups.get(room.id).computers.push(computer);
    });

    return Array.from(groups.values()).sort((a, b) => a.id - b.id);
}

function countStatus(zone, status) {
    return zoneComputers(zone).filter(computer => {
        if (status === 'available') {
            return !['occupied', 'maintenance'].includes(computer.status);
        }

        return computer.status === status;
    }).length;
}

function roomAvailable(room) {
    return room.is_active &&
        room.computers.length > 0 &&
        room.computers.every(computer =>
            !['occupied', 'maintenance'].includes(computer.status)
        );
}

function computerStatusClass(status) {
    if (status === 'maintenance') {
        return 'bg-red-950 text-red-400 border border-red-800';
    }

    if (status === 'occupied') {
        return 'bg-gray-700 text-gray-200 border border-gray-600';
    }

    return 'bg-[#263500] text-[#B7FF00] border border-[#B7FF00]';
}

async function loadZones() {
    loading.value = true;
    error.value = '';

    try {
        const [zonesResponse, computersResponse] = await Promise.all([
            api.get('/zones'),
            api.get('/computers'),
        ]);

        zones.value = zonesResponse.data.data ?? zonesResponse.data;
        computers.value = computersResponse.data.data ?? computersResponse.data;
    } catch (e) {
        error.value =
            e.response?.data?.message ||
            'Не удалось загрузить игровые зоны и компьютеры.';
    } finally {
        loading.value = false;
    }
}

onMounted(loadZones);
</script>
