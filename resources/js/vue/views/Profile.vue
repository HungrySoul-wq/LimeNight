<template>
    <main class="max-w-5xl mx-auto px-6 py-16">
        <p class="text-[#00FA9A] tracking-widest mb-3">LIMENIGHT ACCOUNT</p>
        <h1 class="text-4xl font-bold mb-8">Личный кабинет</h1>

        <div
            v-if="!auth.isAuthenticated"
            class="bg-[#101010] border border-[#292929] rounded-2xl p-8"
        >
            <p class="text-gray-400 mb-5">
                Войди в аккаунт, чтобы просмотреть профиль.
            </p>

            <router-link
                to="/app/login"
                class="inline-block bg-[#B7FF00] text-black px-6 py-3 rounded-lg font-bold"
            >
                Войти
            </router-link>
        </div>

        <div v-else class="space-y-6">
            <div class="grid md:grid-cols-3 gap-6">
                <section class="md:col-span-2 bg-[#101010] border border-[#292929] rounded-2xl p-8">
                    <h2 class="text-2xl font-bold text-[#B7FF00] mb-8">
                        Информация об аккаунте
                    </h2>

                    <div class="space-y-6">
                        <div>
                            <p class="text-gray-500 text-sm mb-1">Имя</p>
                            <p class="text-xl">
                                {{ auth.user?.name || 'Не указано' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm mb-1">Email</p>
                            <p class="text-xl">
                                {{ auth.user?.email || 'Не указан' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-gray-500 text-sm mb-1">Роль</p>
                            <p class="text-xl text-[#00FA9A]">
                                {{ roleName }}
                            </p>
                        </div>
                    </div>
                </section>

                <aside class="bg-[#101010] border border-[#292929] rounded-2xl p-8">
                    <p class="text-gray-400 text-sm mb-3">
                        Игровой баланс
                    </p>

                    <p class="text-3xl font-bold text-[#B7FF00]">
                        {{ formatMoney(balance) }} ₸
                    </p>

                    <p class="text-gray-500 text-sm mt-3">
                        Средства для оплаты игровых сеансов
                    </p>
                </aside>
            </div>

            <!-- Пополнение баланса -->
            <section class="bg-[#101010] border border-[#292929] rounded-2xl p-8">
                <h2 class="text-2xl font-bold mb-2">
                    Пополнение баланса
                </h2>

                <p class="text-gray-400 text-sm mb-7">
                    Выбери сумму и способ пополнения игрового счёта.
                </p>

                <form @submit.prevent="topUp" class="max-w-xl space-y-5">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            Сумма пополнения (₸)
                        </label>

                        <input
                            v-model.number="amount"
                            type="number"
                            min="100"
                            max="50000"
                            step="1"
                            required
                            placeholder="Например, 5000"
                            class="w-full bg-[#080808] border border-[#292929] rounded-lg px-4 py-3 text-white outline-none focus:border-[#B7FF00]"
                        />

                        <p class="text-gray-500 text-xs mt-2">
                            Минимум 100 ₸, максимум 50 000 ₸.
                        </p>
                    </div>

                    <div>
                        <label class="block text-gray-400 text-sm mb-2">
                            Способ оплаты
                        </label>

                        <select
                            v-model="paymentMethod"
                            required
                            class="w-full bg-[#080808] border border-[#292929] rounded-lg px-4 py-3 text-white outline-none focus:border-[#B7FF00]"
                        >
                            <option value="card">Банковская карта</option>
                            <option value="online">Онлайн-оплата</option>
                            <option value="cash">Наличные</option>
                        </select>
                    </div>

                    <p
                        v-if="paymentError"
                        class="text-red-400 text-sm"
                    >
                        {{ paymentError }}
                    </p>

                    <p
                        v-if="paymentSuccess"
                        class="text-[#00FA9A] text-sm"
                    >
                        {{ paymentSuccess }}
                    </p>

                    <button
                        type="submit"
                        :disabled="paymentLoading"
                        class="w-full sm:w-auto bg-[#B7FF00] text-black px-8 py-3 rounded-lg font-bold hover:brightness-110 transition disabled:opacity-50"
                    >
                        {{ paymentLoading ? 'Обработка...' : 'Пополнить баланс' }}
                    </button>
                </form>

                <p class="text-gray-500 text-xs mt-5">
                    Демонстрационная оплата для курсового проекта.
                    Реальное списание с банковской карты не выполняется.
                </p>
            </section>

            <!-- История платежей -->
            <section class="bg-[#101010] border border-[#292929] rounded-2xl p-8">
                <h2 class="text-2xl font-bold mb-6">
                    История операций
                </h2>

                <p v-if="historyLoading" class="text-gray-400">
                    Загружаем историю операций...
                </p>

                <p v-else-if="historyError" class="text-red-400">
                    {{ historyError }}
                </p>

                <p
                    v-else-if="payments.length === 0"
                    class="text-gray-400"
                >
                    Операций пока нет.
                </p>

                <div v-else class="space-y-4">
                    <div
                        v-for="payment in payments"
                        :key="payment.id"
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-[#292929] pb-4 last:border-0"
                    >
                        <div>
                            <p class="font-bold">
                                {{ paymentTitle(payment.type) }}
                            </p>

                            <p class="text-gray-500 text-sm mt-1">
                                {{ paymentMethodName(payment.payment_method) }}
                                <span v-if="payment.created_at">
                                    · {{ formatDate(payment.created_at) }}
                                </span>
                            </p>

                            <p
                                class="text-xs mt-1"
                                :class="payment.status === 'success' ? 'text-[#00FA9A]' : 'text-gray-500'"
                            >
                                {{ payment.status === 'success' ? 'Успешно' : payment.status }}
                            </p>
                        </div>

                        <p
                            class="text-lg font-bold"
                            :class="payment.type === 'booking_refund' || payment.type === 'balance_top_up'
                                ? 'text-[#00FA9A]'
                                : 'text-white'"
                        >
                            {{ payment.type === 'booking_payment' ? '−' : '+' }}{{ formatMoney(payment.amount) }} ₸
                        </p>
                    </div>
                </div>
            </section>

            <!-- Навигация -->
            <aside class="bg-[#101010] border border-[#292929] rounded-2xl p-8">
                <h2 class="text-xl font-bold mb-5">Мой аккаунт</h2>

                <router-link
                    to="/app/bookings"
                    class="block text-gray-300 hover:text-[#B7FF00] mb-5"
                >
                    Мои бронирования →
                </router-link>

                <router-link
                    v-if="auth.isAdmin"
                    to="/app/admin"
                    class="block text-[#B7FF00] font-bold mb-5"
                >
                    Админ-панель →
                </router-link>

                <button
                    @click="logout"
                    class="w-full mt-4 border border-red-900 text-red-400 rounded-lg py-3 hover:bg-red-950 transition"
                >
                    Выйти из аккаунта
                </button>
            </aside>
        </div>
    </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../api';

const auth = useAuthStore();
const router = useRouter();

const amount = ref(1000);
const paymentMethod = ref('card');

const payments = ref([]);
const paymentLoading = ref(false);
const historyLoading = ref(false);

const paymentError = ref('');
const paymentSuccess = ref('');
const historyError = ref('');

const balance = computed(() => {
    return Number(auth.user?.account?.balance ?? 0);
});

const roleName = computed(() => {
    const roles = {
        client: 'Клиент',
        user: 'Пользователь',
        admin: 'Администратор',
        owner: 'Владелец',
    };

    return roles[auth.user?.role] || 'Пользователь';
});

function formatMoney(value) {
    return new Intl.NumberFormat('ru-RU', {
        maximumFractionDigits: 2,
    }).format(Number(value) || 0);
}

function formatDate(value) {
    return new Date(value).toLocaleString('ru-RU');
}

function paymentTitle(type) {
    const titles = {
        balance_top_up: 'Пополнение баланса',
        booking_payment: 'Оплата бронирования',
        booking_refund: 'Возврат за бронирование',
    };

    return titles[type] || 'Операция';
}

function paymentMethodName(method) {
    const methods = {
        card: 'Банковская карта',
        online: 'Онлайн-оплата',
        cash: 'Наличные',
        balance: 'Игровой баланс',
    };

    return methods[method] || method;
}

async function loadPayments() {
    historyLoading.value = true;
    historyError.value = '';

    try {
        const response = await api.get('/payments');
        payments.value = response.data.data ?? response.data;
    } catch (error) {
        historyError.value =
            error.response?.data?.message
            || 'Не удалось загрузить историю операций.';
    } finally {
        historyLoading.value = false;
    }
}

async function topUp() {
    paymentError.value = '';
    paymentSuccess.value = '';

    if (!Number.isFinite(Number(amount.value)) ||
        Number(amount.value) < 100 ||
        Number(amount.value) > 50000) {
        paymentError.value = 'Сумма должна быть от 100 до 50 000 ₸.';
        return;
    }

    paymentLoading.value = true;

    try {
        const response = await api.post('/payments', {
            amount: Number(amount.value),
            payment_method: paymentMethod.value,
        });

        if (!auth.user.account) {
            auth.user.account = { balance: 0 };
        }

        auth.user.account.balance =
            Number(auth.user.account.balance || 0) + Number(amount.value);

        localStorage.setItem('limenight_user', JSON.stringify(auth.user));

        paymentSuccess.value =
            `Баланс успешно пополнен на ${formatMoney(amount.value)} ₸.`;

        amount.value = 1000;

        await loadPayments();
    } catch (error) {
        paymentError.value =
            error.response?.data?.message
            || Object.values(error.response?.data?.errors || {}).flat()[0]
            || 'Не удалось выполнить пополнение.';
    } finally {
        paymentLoading.value = false;
    }
}

async function logout() {
    await auth.logout();
    router.push('/app');
}

onMounted(() => {
    if (auth.isAuthenticated) {
        loadPayments();
    }
});
</script>
