<template>
    <main class="min-h-[75vh] flex items-center justify-center px-6 py-16">
        <form
            @submit.prevent="isRegister ? submitRegister() : submitLogin()"
            class="w-full max-w-md bg-[#101010] border border-[#292929] rounded-2xl p-8"
        >
            <h1 class="text-3xl font-bold text-[#B7FF00] mb-2">
                {{ isRegister ? 'Регистрация LimeNight' : 'Вход в LimeNight' }}
            </h1>

            <p class="text-gray-400 mb-8">
                {{ isRegister ? 'Создай свой аккаунт' : 'Войди в свой аккаунт' }}
            </p>

            <template v-if="isRegister">
                <label class="block text-sm mb-2">Имя</label>
                <input
                    v-model="name"
                    type="text"
                    required
                    minlength="2"
                    maxlength="255"
                    autocomplete="name"
                    class="w-full bg-black border border-[#292929] rounded-lg p-3 mb-5 text-white"
                    placeholder="Введите имя"
                />
            </template>

            <label class="block text-sm mb-2">Email</label>
            <input
                v-model="email"
                type="email"
                required
                autocomplete="email"
                class="w-full bg-black border border-[#292929] rounded-lg p-3 mb-5 text-white"
                placeholder="example@mail.com"
            />

            <label class="block text-sm mb-2">Пароль</label>
            <input
                v-model="password"
                type="password"
                required
                :minlength="isRegister ? 8 : undefined"
                :autocomplete="isRegister ? 'new-password' : 'current-password'"
                class="w-full bg-black border border-[#292929] rounded-lg p-3 mb-5 text-white"
                placeholder="Введите пароль"
            />

            <template v-if="isRegister">
                <label class="block text-sm mb-2">Повторите пароль</label>
                <input
                    v-model="passwordConfirmation"
                    type="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    class="w-full bg-black border border-[#292929] rounded-lg p-3 mb-5 text-white"
                    placeholder="Повторите пароль"
                />
            </template>

            <p v-if="error" class="text-red-400 mb-4">
                {{ error }}
            </p>

            <p v-if="success" class="text-green-400 mb-4">
                {{ success }}
            </p>

            <button
                type="submit"
                :disabled="auth.loading"
                class="w-full bg-[#B7FF00] text-black font-bold p-3 rounded-lg disabled:opacity-50 hover:bg-[#00FA9A] transition"
            >
                {{
                    auth.loading
                        ? (isRegister ? 'Создаём аккаунт...' : 'Входим...')
                        : (isRegister ? 'Зарегистрироваться' : 'Войти')
                }}
            </button>

            <div class="text-center mt-6 text-sm text-gray-400">
                <template v-if="isRegister">
                    Уже есть аккаунт?
                    <button
                        type="button"
                        @click="switchMode(false)"
                        class="text-[#00FA9A] hover:text-[#B7FF00] font-bold ml-1"
                    >
                        Войти
                    </button>
                </template>

                <template v-else>
                    Ещё нет аккаунта?
                    <button
                        type="button"
                        @click="switchMode(true)"
                        class="text-[#00FA9A] hover:text-[#B7FF00] font-bold ml-1"
                    >
                        Зарегистрироваться
                    </button>
                </template>
            </div>
        </form>
    </main>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const auth = useAuthStore();
const router = useRouter();

const isRegister = ref(false);

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');

const error = ref('');
const success = ref('');

function switchMode(register) {
    isRegister.value = register;
    error.value = '';
    success.value = '';
    password.value = '';
    passwordConfirmation.value = '';
}

async function submitLogin() {
    error.value = '';
    success.value = '';

    try {
        await auth.login({
            email: email.value,
            password: password.value,
        });

        router.push('/app/profile');
    } catch (e) {
        error.value = e.response?.data?.message
            || 'Не удалось войти. Проверь email и пароль.';
    }
}

async function submitRegister() {
    error.value = '';
    success.value = '';

    if (password.value.length < 8) {
        error.value = 'Пароль должен содержать не менее 8 символов.';
        return;
    }

    if (password.value !== passwordConfirmation.value) {
        error.value = 'Пароли не совпадают.';
        return;
    }

    try {
        await auth.register({
            name: name.value,
            email: email.value,
            password: password.value,
            password_confirmation: passwordConfirmation.value,
        });

        success.value = 'Аккаунт успешно создан!';
        router.push('/app/profile');
    } catch (e) {
        const errors = e.response?.data?.errors;

        error.value = errors
            ? Object.values(errors).flat().join(' ')
            : e.response?.data?.message || 'Не удалось зарегистрироваться.';
    }
}
</script>
