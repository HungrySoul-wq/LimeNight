<template>
    <main class="max-w-7xl mx-auto px-6 py-16">
        <p class="text-[#00FA9A] tracking-widest mb-3">
            LIMENIGHT MANAGEMENT
        </p>

        <h1 class="text-4xl font-bold mb-4">
            Панель <span class="text-[#B7FF00]">администратора</span>
        </h1>

        <p class="text-gray-400 mb-10">
            Управление игровым клубом, бронированиями и пользователями.
        </p>

        <!-- Статистика -->
        <section class="mb-14">
            <h2 class="text-2xl font-bold mb-6">Краткая статистика</h2>

            <p v-if="loading" class="text-gray-400">
                Загружаем статистику...
            </p>

            <p v-else-if="error" class="text-red-400 mb-5">
                {{ error }}
            </p>

            <div v-else class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div
                    v-for="item in statistics"
                    :key="item.label"
                    class="bg-[#101010] border border-[#292929] rounded-2xl p-6"
                >
                    <p class="text-gray-400 text-sm mb-3">
                        {{ item.label }}
                    </p>

                    <p class="text-3xl font-bold text-[#B7FF00]">
                        {{ formatNumber(item.value) }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Разделы управления -->
        <h2 class="text-2xl font-bold mb-6">Управление клубом</h2>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <router-link
                v-for="item in sections"
                :key="item.path"
                :to="`/app/admin/${item.path}`"
                class="bg-[#101010] border border-[#292929] rounded-2xl p-7 hover:border-[#B7FF00] transition group"
            >
                <div class="mb-6">
                    <AdminIcon :name="item.path" />
                </div>

                <h2 class="text-xl font-bold mb-3 group-hover:text-[#B7FF00] transition">
                    {{ item.title }}
                </h2>

                <p class="text-gray-400 text-sm mb-5">
                    {{ item.description }}
                </p>

                <span class="text-[#00FA9A] text-sm font-bold">
                    Открыть раздел →
                </span>
            </router-link>
        </div>
    </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AdminIcon from '../components/AdminIcon.vue';
import api from '../api';

const loading = ref(true);
const error = ref('');
const stats = ref({});

const statistics = computed(() => [
    {
        label: 'Пользователи',
        value: stats.value.users_count ?? 0,
    },
    {
        label: 'Компьютеры',
        value: stats.value.computers_count ?? 0,
    },
    {
        label: 'Свободные компьютеры',
        value: stats.value.active_computers_count ?? 0,
    },
    {
        label: 'Игровые комнаты',
        value: stats.value.rooms_count ?? 0,
    },
    {
        label: 'Игровые зоны',
        value: stats.value.zones_count ?? 0,
    },
    {
        label: 'Всего бронирований',
        value: stats.value.bookings_count ?? 0,
    },
    {
        label: 'Активные бронирования',
        value: stats.value.active_bookings_count ?? 0,
    },
    {
        label: 'Выручка',
        value: `${formatNumber(stats.value.revenue ?? 0)} ₸`,
    },
]);

function formatNumber(value) {
    return new Intl.NumberFormat('ru-RU').format(value);
}

async function loadStatistics() {
    loading.value = true;
    error.value = '';

    try {
        const response = await api.get('/statistics');
        stats.value = response.data;
    } catch (err) {
        error.value = err.response?.data?.message
            || 'Не удалось загрузить статистику.';
    } finally {
        loading.value = false;
    }
}

const sections = [
    {
        path: 'computers',
        title: 'Компьютеры',
        description: 'Создание, просмотр, редактирование и удаление компьютеров.',
    },
    {
        path: 'zones',
        title: 'Игровые зоны',
        description: 'Управление общим залом, VIP-зоной и буткемпом.',
    },
    {
        path: 'rooms',
        title: 'Игровые комнаты',
        description: 'Управление комнатами и их параметрами.',
    },
    {
        path: 'users',
        title: 'Пользователи',
        description: 'Просмотр, редактирование и удаление аккаунтов.',
    },
    {
        path: 'bookings',
        title: 'Бронирования',
        description: 'Просмотр, изменение статусов и отмена бронирований.',
    },
    {
        path: 'prices',
        title: 'Тарифы и цены',
        description: 'Управление тарифами, стоимостью и продолжительностью сеансов.',
    },
];

onMounted(loadStatistics);
</script>
