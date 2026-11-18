<template>
    <main class="max-w-7xl mx-auto px-6 py-16">
        <router-link
            to="/app/admin"
            class="text-[#00FA9A] hover:text-[#B7FF00] mb-8 inline-block"
        >
            ← Назад к панели администратора
        </router-link>

        <p class="text-[#00FA9A] tracking-widest mb-3">
            LIMENIGHT MANAGEMENT
        </p>

        <h1 class="text-4xl font-bold mb-4">{{ section.title }}</h1>
        <p class="text-gray-400 mb-10">{{ section.description }}</p>

        <div v-if="error" class="mb-6 p-4 rounded-xl border border-red-500/40 text-red-400">
            {{ error }}
        </div>

        <div v-if="success" class="mb-6 p-4 rounded-xl border border-green-500/40 text-green-400">
            {{ success }}
        </div>

        <!-- Статистика -->
        <div v-if="sectionKey === 'statistics'" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <div
                v-for="item in statisticsItems"
                :key="item.key"
                class="bg-[#101010] border border-[#292929] rounded-2xl p-6"
            >
                <p class="text-gray-400 text-sm mb-3">{{ item.label }}</p>
                <p class="text-3xl font-bold text-[#B7FF00]">
                    {{ stats[item.key] ?? 0 }}
                    <span v-if="item.key === 'revenue'" class="text-base">₸</span>
                </p>
            </div>
        </div>

        <!-- Форма создания и редактирования -->
        <div
            v-if="section.fields.length && sectionKey !== 'statistics'"
            class="bg-[#101010] border border-[#292929] rounded-2xl p-6 md:p-8 mb-8"
        >
            <h2 class="text-xl font-bold mb-6">
                {{ editingId ? 'Редактирование записи' : 'Добавить запись' }}
            </h2>

            <form @submit.prevent="saveRecord" class="grid md:grid-cols-2 gap-5">
                <div
                    v-for="field in section.fields"
                    :key="field.key"
                    class="flex flex-col gap-2"
                    :class="field.type === 'textarea' ? 'md:col-span-2' : ''"
                >
                    <label class="text-sm text-gray-300">
                        {{ field.label }}
                    </label>

                    <!-- Выпадающий список -->
                    <select
                        v-if="field.type === 'select'"
                        v-model="form[field.key]"
                        :required="field.required"
                        class="bg-black border border-[#333] rounded-xl px-4 py-3 text-white focus:border-[#B7FF00] outline-none"
                    >
                        <option value="">Выбери вариант</option>
                        <option
                            v-for="option in (field.options || (field.key === 'zone_id' ? zones : rooms))"
                            :key="option.id"
                            :value="String(option.id)"
                        >
                            {{ option.name }}
                        </option>
                    </select>

                    <!-- Многострочное поле -->
                    <textarea
                        v-else-if="field.type === 'textarea'"
                        v-model="form[field.key]"
                        rows="3"
                        :required="field.required"
                        class="bg-black border border-[#333] rounded-xl px-4 py-3 text-white focus:border-[#B7FF00] outline-none"
                    ></textarea>

                    <!-- Переключатель -->
                    <label
                        v-else-if="field.type === 'checkbox'"
                        class="flex items-center gap-3 text-gray-300 py-2"
                    >
                        <input
                            v-model="form[field.key]"
                            type="checkbox"
                            class="accent-[#B7FF00] w-5 h-5"
                        />
                        {{ field.checkboxLabel || 'Активно' }}
                    </label>

                    <!-- Обычное поле -->
                    <input
                        v-else
                        v-model="form[field.key]"
                        :type="field.type || 'text'"
                        :required="field.required && !(editingId && field.key === 'password')"
                        :min="field.min"
                        :step="field.step"
                        :placeholder="field.placeholder || ''"
                        class="bg-black border border-[#333] rounded-xl px-4 py-3 text-white focus:border-[#B7FF00] outline-none"
                    />
                </div>

                <div class="md:col-span-2 flex flex-wrap gap-3">
                    <button
                        type="submit"
                        :disabled="saving"
                        class="bg-[#B7FF00] text-black font-bold px-6 py-3 rounded-xl hover:bg-[#00FA9A] disabled:opacity-50"
                    >
                        {{ saving ? 'Сохранение...' : 'Сохранить' }}
                    </button>

                    <button
                        v-if="editingId"
                        type="button"
                        @click="resetForm"
                        class="border border-[#444] px-6 py-3 rounded-xl hover:border-white"
                    >
                        Отмена
                    </button>
                </div>
            </form>
        </div>

        <!-- Список записей -->
        <div
            v-if="sectionKey !== 'statistics'"
            class="bg-[#101010] border border-[#292929] rounded-2xl p-6"
        >
            <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                <h2 class="text-xl font-bold">Список записей</h2>

                <button
                    @click="loadData"
                    class="text-[#00FA9A] hover:text-[#B7FF00]"
                >
                    Обновить
                </button>
            </div>

            <p v-if="loading" class="text-gray-400">Загрузка данных...</p>

            <p v-else-if="records.length === 0" class="text-gray-400">
                Записей пока нет.
            </p>

            <div v-else class="space-y-4">
                <div
                    v-for="record in records"
                    :key="record.id"
                    class="border border-[#292929] rounded-xl p-5 flex flex-col lg:flex-row lg:items-center justify-between gap-5"
                >
                    <div class="min-w-0">
                        <h3 class="font-bold text-lg mb-2">
                            {{ recordTitle(record) }}
                        </h3>

                        <div class="text-sm text-gray-400 space-y-1">
                            <p v-for="line in recordDetails(record)" :key="line">
                                {{ line }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2 shrink-0">
                        <button
                            v-if="sectionKey !== 'bookings'"
                            @click="editRecord(record)"
                            class="px-4 py-2 rounded-lg border border-[#B7FF00] text-[#B7FF00] hover:bg-[#B7FF00] hover:text-black"
                        >
                            Изменить
                        </button>

                        <button
                            v-if="sectionKey === 'bookings'"
                            @click="editRecord(record)"
                            class="px-4 py-2 rounded-lg border border-[#B7FF00] text-[#B7FF00] hover:bg-[#B7FF00] hover:text-black"
                        >
                            Статус
                        </button>

                        <button
                            v-if="section.deletable"
                            @click="deleteRecord(record)"
                            class="px-4 py-2 rounded-lg border border-red-500/50 text-red-400 hover:bg-red-500 hover:text-white"
                        >
                            Удалить
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Последние бронирования -->
        <div
            v-if="sectionKey === 'statistics' && stats.recent_bookings"
            class="mt-8 bg-[#101010] border border-[#292929] rounded-2xl p-6"
        >
            <h2 class="text-xl font-bold mb-5">Последние бронирования</h2>

            <div
                v-for="booking in stats.recent_bookings"
                :key="booking.id"
                class="border-b border-[#292929] py-4 last:border-0"
            >
                <p class="font-bold">Бронирование №{{ booking.id }}</p>
                <p class="text-sm text-gray-400">
                    {{ booking.user?.name || 'Пользователь' }}
                    · {{ booking.status }}
                </p>
            </div>
        </div>
    </main>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { useRoute } from 'vue-router';
import api from '../api';

const route = useRoute();

const sectionKey = computed(() => route.params.section);

const configs = {
    computers: {
        title: 'Компьютеры',
        description: 'Управление компьютерами игрового клуба.',
        endpoint: 'computers',
        deletable: true,
        fields: [
            { key: 'zone_id', label: 'Игровая зона', type: 'select', required: true },
            { key: 'room_id', label: 'Игровая комната', type: 'select' },
            { key: 'pc_number', label: 'Номер компьютера', type: 'number', required: true, min: 1 },
            { key: 'ip_address', label: 'IP-адрес' },
            {
                key: 'status',
                label: 'Статус компьютера',
                type: 'select',
                required: true,
                options: [
                    { id: 'available', name: 'Доступен' },
                    { id: 'occupied', name: 'Занят' },
                    { id: 'maintenance', name: 'На обслуживании' },
                ],
            },
            { key: 'specs', label: 'Характеристики (JSON)', type: 'textarea' },
        ],
    },

    zones: {
        title: 'Игровые зоны',
        description: 'Создание и редактирование игровых зон.',
        endpoint: 'zones',
        deletable: true,
        fields: [
            { key: 'name', label: 'Название', required: true },
            { key: 'hourly_rate', label: 'Цена за час', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'price_1_hour', label: 'Цена за 1 час', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'price_3_hours', label: 'Цена за 3 часа', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'price_5_hours', label: 'Цена за 5 часов', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'price_night', label: 'Ночной тариф', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'description', label: 'Описание', type: 'textarea' },
        ],
    },

    rooms: {
        title: 'Игровые комнаты',
        description: 'Управление комнатами клуба.',
        endpoint: 'rooms',
        deletable: true,
        fields: [
            { key: 'zone_id', label: 'Игровая зона', type: 'select', required: true },
            { key: 'name', label: 'Название комнаты', required: true },
            {
                key: 'type',
                label: 'Тип комнаты',
                type: 'select',
                required: true,
                options: [
                    { id: 'standard', name: 'Обычная' },
                    { id: 'vip', name: 'VIP' },
                    { id: 'bootcamp', name: 'Bootcamp' },
                ],
            },
            { key: 'capacity', label: 'Вместимость', type: 'number', required: true, min: 1 },
            { key: 'is_active', label: 'Активность', type: 'checkbox', checkboxLabel: 'Комната активна' },
        ],
    },

    users: {
        title: 'Пользователи',
        description: 'Управление аккаунтами пользователей.',
        endpoint: 'users',
        deletable: true,
        fields: [
            { key: 'name', label: 'Имя', required: true },
            { key: 'email', label: 'Email', type: 'email', required: true },
            { key: 'password', label: 'Пароль', type: 'password', required: true },
            { key: 'phone', label: 'Телефон' },
            {
                key: 'role',
                label: 'Роль',
                type: 'select',
                required: true,
                options: [
                    { id: 'client', name: 'Клиент' },
                    { id: 'admin', name: 'Администратор' },
                    { id: 'owner', name: 'Владелец' },
                ],
            },
        ],
    },

    bookings: {
        title: 'Бронирования',
        description: 'Просмотр бронирований и изменение их статусов.',
        endpoint: 'bookings',
        deletable: false,
        fields: [
            {
                key: 'status',
                label: 'Статус бронирования',
                type: 'select',
                required: true,
                options: [
                    { id: 'pending', name: 'Ожидает подтверждения' },
                    { id: 'confirmed', name: 'Подтверждено' },
                    { id: 'active', name: 'Активно' },
                    { id: 'completed', name: 'Завершено' },
                    { id: 'cancelled', name: 'Отменено' },
                ],
            },
        ],
    },

    prices: {
        title: 'Тарифы и цены',
        description: 'Управление тарифами игровых зон.',
        endpoint: 'zones',
        deletable: true,
        fields: [
            { key: 'name', label: 'Название зоны', required: true },
            { key: 'hourly_rate', label: 'Цена за час', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'price_1_hour', label: 'Цена за 1 час', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'price_3_hours', label: 'Цена за 3 часа', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'price_5_hours', label: 'Цена за 5 часов', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'price_night', label: 'Ночной тариф', type: 'number', required: true, min: 0, step: '0.01' },
            { key: 'description', label: 'Описание', type: 'textarea' },
        ],
    },
};

const section = computed(() => configs[sectionKey.value] || {
    title: 'Раздел не найден',
    description: 'Такого раздела администрирования не существует.',
    endpoint: '',
    fields: [],
    deletable: false,
});

const records = ref([]);
const stats = ref({});
const zones = ref([]);
const rooms = ref([]);

const loading = ref(false);
const saving = ref(false);
const editingId = ref(null);
const error = ref('');
const success = ref('');

const form = reactive({});

const statisticsItems = [
    { key: 'users_count', label: 'Пользователи' },
    { key: 'computers_count', label: 'Компьютеры' },
    { key: 'active_computers_count', label: 'Доступные компьютеры' },
    { key: 'zones_count', label: 'Игровые зоны' },
    { key: 'bookings_count', label: 'Всего бронирований' },
    { key: 'active_bookings_count', label: 'Активные бронирования' },
    { key: 'completed_bookings_count', label: 'Завершённые бронирования' },
    { key: 'cancelled_bookings_count', label: 'Отменённые бронирования' },
    { key: 'revenue', label: 'Выручка' },
];

function extractRows(response) {
    const data = response.data.data ?? response.data;
    return Array.isArray(data) ? data : (data.data ?? []);
}

function resetForm() {
    editingId.value = null;

    Object.keys(form).forEach(key => delete form[key]);

    section.value.fields.forEach(field => {
        form[field.key] = field.type === 'checkbox' ? true : '';
    });
}

function recordTitle(record) {
    if (sectionKey.value === 'users') return record.name || `Пользователь №${record.id}`;
    if (sectionKey.value === 'bookings') return `Бронирование №${record.id}`;
    if (sectionKey.value === 'computers') return `Компьютер №${record.pc_number}`;
    return record.name || `Запись №${record.id}`;
}

function recordDetails(record) {
    if (sectionKey.value === 'users') {
        return [
            `Email: ${record.email || '—'}`,
            `Роль: ${record.role || '—'}`,
            `Телефон: ${record.phone || '—'}`,
        ];
    }

    if (sectionKey.value === 'bookings') {
        return [
            `Клиент: ${record.user?.name || '—'}`,
            `Компьютер: ${record.computer?.pc_number || '—'}`,
            `Статус: ${record.status || '—'}`,
            `Стоимость: ${record.total_price ?? '—'} ₸`,
        ];
    }

    if (sectionKey.value === 'computers') {
        const statuses = {
            available: 'Доступен',
            occupied: 'Занят',
            maintenance: 'На обслуживании',
        };

        return [
            `Зона: ${record.zone?.name || record.zone_id || '—'}`,
            `Статус: ${statuses[record.status] || record.status || '—'}`,
            `IP: ${record.ip_address || '—'}`,
        ];
    }

    if (sectionKey.value === 'rooms') {
        return [
            `Зона: ${record.zone?.name || record.zone_id || '—'}`,
            `Тип: ${record.type || '—'}`,
            `Вместимость: ${record.capacity || '—'}`,
            `Активна: ${record.is_active ? 'Да' : 'Нет'}`,
        ];
    }

    return [
        `Цена за час: ${record.hourly_rate ?? '—'} ₸`,
        `Цена за 1 час: ${record.price_1_hour ?? '—'} ₸`,
        `Цена за 3 часа: ${record.price_3_hours ?? '—'} ₸`,
        `Цена за 5 часов: ${record.price_5_hours ?? '—'} ₸`,
        `Ночной тариф: ${record.price_night ?? '—'} ₸`,
        `Описание: ${record.description || '—'}`,
    ];
}

async function loadData() {
    error.value = '';
    loading.value = true;

    try {
        if (sectionKey.value === 'statistics') {
            const response = await api.get('/statistics');
            stats.value = response.data;
        } else {
            const response = await api.get(`/${section.value.endpoint}`);
            records.value = extractRows(response);
        }

        if (['computers', 'rooms'].includes(sectionKey.value)) {
            const [zonesResponse, roomsResponse] = await Promise.all([
                api.get('/zones'),
                api.get('/rooms'),
            ]);

            zones.value = extractRows(zonesResponse);
            rooms.value = extractRows(roomsResponse);
        }
    } catch (err) {
        error.value = err.response?.data?.message || 'Не удалось загрузить данные.';
    } finally {
        loading.value = false;
    }
}

function editRecord(record) {
    editingId.value = record.id;

    Object.keys(form).forEach(key => delete form[key]);

    section.value.fields.forEach(field => {
        const value = record[field.key];

        form[field.key] = field.key === 'password'
            ? ''
            : field.key === 'specs' && value && typeof value === 'object'
                ? JSON.stringify(value, null, 2)
                : value === null || value === undefined
                    ? (field.type === 'checkbox' ? false : '')
                    : String(value);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function saveRecord() {
    error.value = '';
    success.value = '';
    saving.value = true;

    try {
        const data = {};

        section.value.fields.forEach(field => {
            const value = form[field.key];

            if (
                value !== '' &&
                value !== undefined &&
                !(editingId.value && field.key === 'password')
            ) {
                data[field.key] = value;
            }
        });

        if (sectionKey.value === 'computers' && data.specs) {
            data.specs = JSON.parse(data.specs);
        }

        if (sectionKey.value === 'rooms' && data.is_active !== undefined) {
            data.is_active = Boolean(data.is_active);
        }

        if (sectionKey.value === 'computers' && data.room_id === '') {
            data.room_id = null;
        }

        if (editingId.value) {
            await api.put(`/${section.value.endpoint}/${editingId.value}`, data);
            success.value = 'Запись успешно обновлена.';
        } else {
            await api.post(`/${section.value.endpoint}`, data);
            success.value = 'Запись успешно создана.';
        }

        resetForm();
        await loadData();
    } catch (err) {
        error.value = err.response?.data?.message
            || (err instanceof SyntaxError
                ? 'Проверь формат JSON характеристик.'
                : 'Не удалось сохранить запись.');
    } finally {
        saving.value = false;
    }
}

async function deleteRecord(record) {
    if (!window.confirm('Ты действительно хочешь удалить эту запись?')) return;

    error.value = '';
    success.value = '';

    try {
        await api.delete(`/${section.value.endpoint}/${record.id}`);
        success.value = 'Запись удалена.';
        await loadData();
    } catch (err) {
        error.value = err.response?.data?.message || 'Не удалось удалить запись.';
    }
}

watch(sectionKey, () => {
    resetForm();
    records.value = [];
    stats.value = {};
    success.value = '';
    loadData();
});

onMounted(() => {
    resetForm();
    loadData();
});
</script>
