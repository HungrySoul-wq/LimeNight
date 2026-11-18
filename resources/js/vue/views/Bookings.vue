<template>
    <main class="max-w-7xl mx-auto px-6 py-12">
        <p class="text-[#B7FF00] tracking-widest mb-3">LIMENIGHT</p>
        <h1 class="text-4xl font-bold mb-4">Бронирование компьютеров</h1>
        <p class="text-gray-400 mb-10">
            Выбери игровую зону, компьютер или комнату и время посещения.
        </p>

        <div v-if="pageLoading" class="text-[#00FA9A]">
            Загружаем игровые зоны и компьютеры...
        </div>

        <div v-else-if="pageError" class="bg-[#101010] border border-red-900 rounded-xl p-6 mb-8">
            <p class="text-red-400">{{ pageError }}</p>
            <button @click="loadPage" class="mt-4 bg-[#B7FF00] text-black px-5 py-2 rounded-lg font-bold">
                Повторить
            </button>
        </div>

        <template v-else>
            <section class="bg-[#101010] border border-[#292929] rounded-xl p-6 mb-6">
                <h2 class="text-2xl font-bold mb-6">Параметры бронирования</h2>

                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-400 mb-2">Дата и время начала</label>
                        <input
                            v-model="startTime"
                            type="datetime-local"
                            :min="minimumDateTime"
                            step="900"
                            class="w-full bg-[#181818] border border-[#393939] rounded-lg px-4 py-3 text-white"
                        />
                    </div>

                    <div>
                        <label class="block text-gray-400 mb-2">Тариф</label>
                        <select
                            v-model="selectedPackage"
                            class="w-full bg-[#181818] border border-[#393939] rounded-lg px-4 py-3 text-white"
                        >
                            <option value="1_hour">1 час</option>
                            <option value="3_hours">3 часа</option>
                            <option value="5_hours">5 часов</option>
                            <option value="night">Ночной тариф (10 часов)</option>
                        </select>
                    </div>
                </div>

                <p class="text-gray-500 text-sm mt-4">
                    Доступность компьютеров обновляется автоматически при изменении времени и тарифа.
                    Между бронированиями предусмотрен промежуток 15 минут.
                </p>
            </section>

            <!-- Подтверждение бронирования теперь находится сверху -->
            <section v-if="selectedTarget" class="bg-[#101010] border border-[#B7FF00] rounded-xl p-6 mb-8">
                <h2 class="text-2xl font-bold text-[#B7FF00] mb-4">
                    Выбрано для бронирования
                </h2>

                <p class="text-white text-lg">
                    {{ selectedTarget.type === 'room'
                        ? selectedTarget.room.name
                        : 'Компьютер №' + selectedTarget.computer.pc_number }}
                </p>

                <p class="text-gray-400 mt-2">
                    {{ displayZoneName(selectedTarget.zone.name) }}
                </p>

                <p class="text-[#00FA9A] font-bold text-xl mt-4">
                    Стоимость: {{ formatPrice(selectedPrice) }} ₸
                </p>

                <p v-if="bookingError" class="text-red-400 mt-4">
                    {{ bookingError }}
                </p>

                <p v-if="bookingSuccess" class="text-[#00FA9A] mt-4">
                    {{ bookingSuccess }}
                </p>

                <button
                    @click="createBooking"
                    :disabled="creatingBooking || !auth.isAuthenticated"
                    class="mt-6 bg-[#B7FF00] text-black px-7 py-3 rounded-lg font-bold hover:bg-[#a5e600] disabled:opacity-50"
                >
                    {{ creatingBooking ? 'Создаём бронирование...' : 'Забронировать' }}
                </button>

                <p v-if="!auth.isAuthenticated" class="text-gray-400 mt-4">
                    Для бронирования необходимо
                    <router-link to="/app/login" class="text-[#B7FF00] font-bold">войти в аккаунт</router-link>.
                </p>

                <button @click="selectedTarget = null" class="block text-gray-500 mt-4 hover:text-white">
                    Снять выбор
                </button>
            </section>

            <section class="flex flex-wrap gap-5 mb-8 text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-[#B7FF00]"></span>
                    <span class="text-gray-300">Свободен</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-orange-500"></span>
                    <span class="text-gray-300">Забронирован</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-gray-500"></span>
                    <span class="text-gray-300">Занят</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded bg-red-600"></span>
                    <span class="text-gray-300">Неисправен</span>
                </div>
            </section>

            <section v-for="zone in zones" :key="zone.id" class="mb-12">
                <div class="flex flex-wrap justify-between items-end gap-3 mb-6">
                    <div>
                        <p class="text-[#00FA9A] text-sm tracking-widest mb-2">ИГРОВАЯ ЗОНА</p>
                        <h2 class="text-3xl font-bold text-[#B7FF00]">
                            {{ displayZoneName(zone.name) }}
                        </h2>
                        <p class="text-gray-400 mt-2">{{ zone.description }}</p>
                    </div>

                    <p class="text-[#00FA9A] font-bold text-xl">
                        {{ formatPrice(zone.hourly_rate) }} ₸ / час
                    </p>
                </div>

                <div
                    v-for="room in roomsForZone(zone)"
                    :key="room.id"
                    class="bg-[#101010] border border-[#292929] rounded-xl p-6 mb-6"
                >
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                        <div>
                            <h3 class="text-xl font-bold">{{ room.name }}</h3>
                            <p class="text-gray-500 text-sm mt-1">
                                {{ room.computers.length }} компьютеров
                                <span v-if="room.type === 'bootcamp'">
                                    · Бронирование всей комнаты
                                </span>
                            </p>
                        </div>

                        <button
                            v-if="room.type === 'bootcamp'"
                            @click="selectRoom(room, zone)"
                            :disabled="!isRoomAvailable(room)"
                            class="bg-[#B7FF00] text-black px-5 py-2 rounded-lg font-bold hover:bg-[#a5e600] disabled:opacity-40 disabled:cursor-not-allowed"
                        >
                            {{ isRoomAvailable(room) ? 'Выбрать всю комнату' : 'Комната недоступна' }}
                        </button>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3">
                        <button
                            v-for="computer in room.computers"
                            :key="computer.id"
                            @click="selectComputer(computer, zone)"
                            :disabled="room.type === 'bootcamp' || !isComputerAvailable(computer)"
                            :class="[
                                'rounded-lg p-3 text-center border transition',
                                computerStatus(computer).class,
                                selectedTarget?.type === 'computer' && selectedTarget.id === computer.id
                                    ? 'ring-2 ring-white'
                                    : '',
                                room.type === 'bootcamp' ? 'cursor-default opacity-70' : ''
                            ]"
                        >
                            <span class="block text-lg font-bold">
                                №{{ computer.pc_number }}
                            </span>
                            <span class="block text-xs mt-1">
                                {{ computerStatus(computer).label }}
                            </span>
                        </button>
                    </div>

                    <p v-if="room.type === 'bootcamp'" class="text-gray-500 text-sm mt-4">
                        Компьютеры этой комнаты нельзя бронировать отдельно.
                    </p>
                </div>

                <div
                    v-if="roomsForZone(zone).length === 0"
                    class="bg-[#101010] border border-[#292929] rounded-xl p-6 text-gray-500"
                >
                    В этой зоне пока нет компьютеров.
                </div>
            </section>

            <section v-if="unassignedComputers.length" class="bg-[#101010] border border-[#292929] rounded-xl p-6 mb-10">
                <h2 class="text-xl font-bold mb-5">Компьютеры без комнаты</h2>

                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-8 gap-3">
                    <button
                        v-for="computer in unassignedComputers"
                        :key="computer.id"
                        @click="selectComputer(computer, findZone(computer.zone?.id ?? computer.zone_id))"
                        :disabled="!isComputerAvailable(computer)"
                        :class="[
                            'rounded-lg p-3 border transition',
                            computerStatus(computer).class,
                            selectedTarget?.type === 'computer' && selectedTarget.id === computer.id
                                ? 'ring-2 ring-white'
                                : ''
                        ]"
                    >
                        <span class="block font-bold">№{{ computer.pc_number }}</span>
                        <span class="block text-xs mt-1">{{ computerStatus(computer).label }}</span>
                    </button>
                </div>
            </section>
        </template>

        <section class="mt-16">
            <h2 class="text-3xl font-bold mb-8">Мои бронирования</h2>

            <div v-if="!auth.isAuthenticated" class="bg-[#101010] border border-[#292929] rounded-xl p-8">
                <p class="text-gray-400 mb-4">Войди в аккаунт, чтобы просмотреть свои бронирования.</p>
                <router-link to="/app/login" class="inline-block bg-[#B7FF00] text-black px-5 py-3 rounded-lg font-bold">
                    Войти
                </router-link>
            </div>

            <div v-else-if="bookingsLoading" class="text-[#00FA9A]">
                Загружаем бронирования...
            </div>

            <div v-else-if="bookingsError" class="bg-[#101010] border border-red-900 rounded-xl p-6">
                <p class="text-red-400">{{ bookingsError }}</p>
                <button @click="loadBookings" class="mt-4 bg-[#B7FF00] text-black px-5 py-2 rounded-lg font-bold">
                    Повторить
                </button>
            </div>

            <div v-else-if="bookings.length === 0" class="bg-[#101010] border border-[#292929] rounded-xl p-8">
                <p class="text-gray-400">У тебя пока нет бронирований.</p>
            </div>

            <div v-else class="space-y-4">
                <article
                    v-for="booking in bookings"
                    :key="booking.id"
                    class="bg-[#101010] border border-[#292929] rounded-xl p-6"
                >
                    <div class="flex flex-wrap justify-between gap-3 mb-4">
                        <h3 class="text-xl font-bold text-[#B7FF00]">
                            Бронирование №{{ booking.id }}
                        </h3>
                        <span class="text-[#00FA9A]">{{ statusName(booking.status) }}</span>
                    </div>

                    <p class="text-gray-300 mb-2">
                        {{ booking.room?.name || booking.computer?.room?.name || 'Компьютер' }}
                    </p>

                    <p class="text-gray-400 mb-2">
                        Зона: {{ displayZoneName(booking.computer?.zone?.name || booking.room?.zone?.name) }}
                    </p>

                    <p v-if="booking.computer" class="text-gray-400 mb-2">
                        Компьютер №{{ booking.computer.pc_number }}
                    </p>

                    <p v-if="booking.booking_type === 'room'" class="text-gray-400 mb-2">
                        Забронирована вся комната
                    </p>

                    <p class="text-gray-400 mb-2">
                        Начало: {{ formatDate(booking.start_time) }}
                    </p>

                    <p class="text-gray-400 mb-2">
                        Окончание: {{ formatDate(booking.end_time) }}
                    </p>

                    <p class="text-[#00FA9A] font-bold mt-4">
                        {{ formatPrice(booking.total_price) }} ₸
                    </p>

                    <button
                        v-if="['pending', 'confirmed', 'active'].includes(booking.status)"
                        @click="cancelBooking(booking.id)"
                        :disabled="cancellingId === booking.id"
                        class="mt-5 border border-red-900 text-red-400 px-5 py-2 rounded-lg hover:bg-red-950 disabled:opacity-50"
                    >
                        {{ cancellingId === booking.id ? 'Отменяем...' : 'Отменить бронирование' }}
                    </button>
                </article>
            </div>
        </section>
    </main>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const route = useRoute();

const zones = ref([]);
const computers = ref([]);
const bookings = ref([]);

const pageLoading = ref(true);
const pageError = ref('');

const bookingsLoading = ref(false);
const bookingsError = ref('');

const selectedTarget = ref(null);
const selectedPackage = ref('1_hour');
const startTime = ref(getDefaultStartTime());

const creatingBooking = ref(false);
const bookingError = ref('');
const bookingSuccess = ref('');
const cancellingId = ref(null);

const packageHours = {
    '1_hour': 1,
    '3_hours': 3,
    '5_hours': 5,
    night: 10,
};

function pad(value) {
    return String(value).padStart(2, '0');
}

function getDefaultStartTime() {
    const date = new Date();
    date.setSeconds(0, 0);

    const minutes = date.getMinutes();
    date.setMinutes(Math.ceil((minutes + 1) / 15) * 15);

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

const minimumDateTime = ref(getDefaultStartTime());

const selectedPrice = computed(() => {
    if (!selectedTarget.value) return 0;

    const zone = selectedTarget.value.zone;

    const prices = {
        '1_hour': zone.price_1_hour,
        '3_hours': zone.price_3_hours,
        '5_hours': zone.price_5_hours,
        night: zone.price_night,
    };

    return Number(prices[selectedPackage.value] ?? zone.hourly_rate ?? 0);
});

const unassignedComputers = computed(() => {
    return computers.value.filter(computer => !computer.room_id && !computer.room);
});

function displayZoneName(name) {
    return name === 'COMMON' ? 'Общий зал' : (name || 'Игровая зона');
}

function formatPrice(price) {
    return Number(price || 0).toLocaleString('ru-RU');
}

function findZone(id) {
    return zones.value.find(zone => Number(zone.id) === Number(id));
}

function roomsForZone(zone) {
    const groups = new Map();

    computers.value
        .filter(computer => Number(computer.zone?.id ?? computer.zone_id) === Number(zone.id))
        .filter(computer => computer.room)
        .forEach(computer => {
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

function getSelectedInterval() {
    if (!startTime.value) return null;

    const start = new Date(startTime.value);

    if (Number.isNaN(start.getTime())) return null;

    const hours = packageHours[selectedPackage.value] || 1;
    const end = new Date(start.getTime() + hours * 60 * 60 * 1000);

    return {
        start: new Date(start.getTime() - 15 * 60 * 1000),
        end: new Date(end.getTime() + 15 * 60 * 1000),
    };
}

function findConflictingBooking(computer) {
    const interval = getSelectedInterval();

    if (!interval) return null;

    return (computer.bookings || []).find(booking => {
        if (!['pending', 'confirmed', 'active'].includes(booking.status)) {
            return false;
        }

        const bookingStart = new Date(booking.start_time);
        const bookingEnd = new Date(booking.end_time);

        return bookingStart < interval.end && bookingEnd > interval.start;
    }) || null;
}

function isComputerAvailable(computer) {
    if (computer.status === 'maintenance' || computer.status === 'occupied') {
        return false;
    }

    return !findConflictingBooking(computer);
}

function isRoomAvailable(room) {
    if (!room.is_active || room.computers.length !== 5) {
        return false;
    }

    return room.computers.every(computer => isComputerAvailable(computer));
}

function computerStatus(computer) {
    if (computer.status === 'maintenance') {
        return {
            label: 'Неисправен',
            class: 'bg-red-950 border-red-800 text-red-400',
        };
    }

    if (computer.status === 'occupied') {
        return {
            label: 'Занят',
            class: 'bg-gray-700 border-gray-600 text-gray-200',
        };
    }

    const conflict = findConflictingBooking(computer);

    if (conflict) {
        return {
            label: `До ${new Date(conflict.end_time).toLocaleTimeString('ru-RU', {
                hour: '2-digit',
                minute: '2-digit',
            })}`,
            class: 'bg-orange-950 border-orange-700 text-orange-300',
        };
    }

    return {
        label: 'Свободен',
        class: 'bg-[#263500] border-[#B7FF00] text-[#B7FF00]',
    };
}

function selectComputer(computer, zone) {
    if (!auth.isAuthenticated) {
        bookingError.value = 'Войди в аккаунт, чтобы оформить бронирование.';
        return;
    }

    if (!zone || !isComputerAvailable(computer)) return;

    selectedTarget.value = {
        type: 'computer',
        id: computer.id,
        computer,
        zone,
    };

    bookingError.value = '';
    bookingSuccess.value = '';
}

function selectRoom(room, zone) {
    if (!auth.isAuthenticated) {
        bookingError.value = 'Войди в аккаунт, чтобы оформить бронирование.';
        return;
    }

    if (!isRoomAvailable(room)) return;

    selectedTarget.value = {
        type: 'room',
        id: room.id,
        room,
        zone,
    };

    bookingError.value = '';
    bookingSuccess.value = '';
}

async function loadPage() {
    pageLoading.value = true;
    pageError.value = '';

    try {
        const [zonesResponse, computersResponse] = await Promise.all([
            api.get('/zones'),
            api.get('/computers'),
        ]);

        zones.value = zonesResponse.data.data ?? zonesResponse.data;
        computers.value = computersResponse.data.data ?? computersResponse.data;

        if (route.query.zone) {
            const selectedZone = findZone(route.query.zone);

            if (selectedZone) {
                const firstComputer = computers.value.find(computer =>
                    Number(computer.zone?.id ?? computer.zone_id) === Number(selectedZone.id)
                    && computer.room?.type !== 'bootcamp'
                    && isComputerAvailable(computer)
                );

                if (firstComputer) {
                    selectComputer(firstComputer, selectedZone);
                }
            }
        }
    } catch (e) {
        pageError.value = e.response?.data?.message ||
            'Не удалось загрузить игровые зоны и компьютеры.';
    } finally {
        pageLoading.value = false;
    }
}

async function loadBookings() {
    if (!auth.isAuthenticated) return;

    bookingsLoading.value = true;
    bookingsError.value = '';

    try {
        const response = await api.get('/bookings');
        const data = response.data.data ?? response.data;
        bookings.value = Array.isArray(data) ? data : [];
    } catch (e) {
        bookingsError.value = e.response?.data?.message ||
            'Не удалось загрузить бронирования.';
    } finally {
        bookingsLoading.value = false;
    }
}

function statusName(status) {
    const statuses = {
        pending: 'Ожидает подтверждения',
        confirmed: 'Подтверждено',
        active: 'Активно',
        cancelled: 'Отменено',
        completed: 'Завершено',
        expired: 'Истекло',
    };

    return statuses[status] || status || 'Не указан';
}

function formatDate(value) {
    return value ? new Date(value).toLocaleString('ru-RU') : '—';
}

async function createBooking() {
    if (!auth.isAuthenticated || !selectedTarget.value) return;

    bookingError.value = '';
    bookingSuccess.value = '';

    if (!startTime.value) {
        bookingError.value = 'Выбери дату и время начала.';
        return;
    }

    const start = new Date(startTime.value);

    if (Number.isNaN(start.getTime()) || start < new Date()) {
        bookingError.value = 'Нельзя выбрать прошедшее время.';
        return;
    }

    if (![0, 15, 30, 45].includes(start.getMinutes())) {
        bookingError.value = 'Выбери время с шагом 15 минут.';
        return;
    }

    if (
        selectedPackage.value === 'night' &&
        ![0, 22, 23].includes(start.getHours())
    ) {
        bookingError.value = 'Ночной тариф можно начать только в 22:00, 23:00 или 00:00.';
        return;
    }

    if (
        selectedPackage.value !== 'night' &&
        (start.getHours() < 8 || start.getHours() > 23)
    ) {
        bookingError.value = 'Обычное бронирование доступно с 08:00 до 00:00.';
        return;
    }

    if (selectedTarget.value.type === 'computer') {
        if (!isComputerAvailable(selectedTarget.value.computer)) {
            bookingError.value = 'Компьютер уже занят на выбранное время. Выбери другой.';
            return;
        }
    } else if (!isRoomAvailable(selectedTarget.value.room)) {
        bookingError.value = 'Комната Bootcamp недоступна на выбранное время.';
        return;
    }

    creatingBooking.value = true;

    try {
        const payload = {
            start_time: startTime.value,
            package: selectedPackage.value,
        };

        if (selectedTarget.value.type === 'room') {
            payload.room_id = selectedTarget.value.id;
        } else {
            payload.computer_id = selectedTarget.value.id;
        }

        await api.post('/bookings', payload);

        bookingSuccess.value = 'Бронирование успешно создано!';
        selectedTarget.value = null;

        await Promise.all([loadBookings(), loadPage()]);
    } catch (e) {
        bookingError.value = e.response?.data?.message ||
            'Не удалось создать бронирование.';
    } finally {
        creatingBooking.value = false;
    }
}

async function cancelBooking(id) {
    if (!window.confirm('Ты действительно хочешь отменить бронирование?')) {
        return;
    }

    cancellingId.value = id;
    bookingsError.value = '';

    try {
        await api.delete(`/bookings/${id}`);
        await Promise.all([loadBookings(), loadPage()]);
    } catch (e) {
        bookingsError.value = e.response?.data?.message ||
            'Не удалось отменить бронирование.';
    } finally {
        cancellingId.value = null;
    }
}

watch([startTime, selectedPackage], () => {
    bookingError.value = '';

    if (selectedTarget.value?.type === 'computer') {
        if (!isComputerAvailable(selectedTarget.value.computer)) {
            selectedTarget.value = null;
        }
    }

    if (selectedTarget.value?.type === 'room') {
        if (!isRoomAvailable(selectedTarget.value.room)) {
            selectedTarget.value = null;
        }
    }
});

onMounted(() => {
    loadPage();

    if (auth.isAuthenticated) {
        loadBookings();
    }
});
</script>
