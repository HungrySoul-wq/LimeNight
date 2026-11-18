<template>
    <div class="min-h-screen bg-black text-white">
        <header class="border-b border-[#292929] bg-[#101010]">
            <div class="max-w-7xl mx-auto px-6 py-5 flex flex-wrap items-center justify-between gap-4">
                <router-link to="/app" class="text-3xl font-bold text-[#B7FF00]">
                    LimeNight
                </router-link>

                <nav class="flex flex-wrap items-center gap-5 text-sm">
                    <router-link to="/app" class="hover:text-[#B7FF00]">
                        Главная
                    </router-link>

                    <router-link to="/app/zones" class="hover:text-[#B7FF00]">
                        Игровые зоны
                    </router-link>

                    <router-link to="/app/bookings" class="hover:text-[#B7FF00]">
                        Бронирования
                    </router-link>

                    <router-link to="/app/profile" class="hover:text-[#B7FF00]">
                        Профиль
                    </router-link>

                    <template v-if="auth.isAuthenticated">
                        <router-link
                            v-if="auth.isAdmin"
                            to="/app/admin"
                            class="text-[#B7FF00] font-bold hover:text-white"
                        >
                            Админ-панель
                        </router-link>

                        <router-link
                            to="/app/profile"
                            class="text-[#00FA9A] font-bold hover:text-[#B7FF00]"
                        >
                            {{ auth.user?.name || 'Мой аккаунт' }}
                        </router-link>

                        <button
                            @click="logout"
                            class="text-gray-400 hover:text-red-400"
                        >
                            Выйти
                        </button>
                    </template>

                    <router-link
                        v-else
                        to="/app/login"
                        class="text-[#B7FF00] hover:text-white"
                    >
                        Войти
                    </router-link>
                </nav>
            </div>
        </header>

        <router-view />

        <footer class="border-t border-[#292929] mt-20 py-8 text-center text-gray-500">
            © 2026 LimeNight. Computer Club.
        </footer>
    </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';

const auth = useAuthStore();
const router = useRouter();

async function logout() {
    await auth.logout();
    router.push('/app');
}
</script>
