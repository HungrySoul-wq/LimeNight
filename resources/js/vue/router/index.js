import { createRouter, createWebHistory } from 'vue-router';

import Home from '../views/Home.vue';
import Zones from '../views/Zones.vue';
import Bookings from '../views/Bookings.vue';
import Profile from '../views/Profile.vue';
import Login from '../views/Login.vue';
import Admin from '../views/Admin.vue';
import AdminSection from '../views/AdminSection.vue';

import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/app',
        component: Home,
    },
    {
        path: '/app/zones',
        component: Zones,
    },
    {
        path: '/app/bookings',
        component: Bookings,
        meta: { requiresAuth: true },
    },
    {
        path: '/app/profile',
        component: Profile,
        meta: { requiresAuth: true },
    },
    {
        path: '/app/login',
        component: Login,
    },
    {
        path: '/app/admin',
        component: Admin,
        meta: { requiresAuth: true, adminOnly: true },
    },
    {
        path: '/app/admin/:section',
        component: AdminSection,
        meta: { requiresAuth: true, adminOnly: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return {
            path: '/app/login',
            query: { redirect: to.fullPath },
        };
    }

    if (to.meta.adminOnly && !auth.isAdmin) {
        return '/app';
    }
});

export default router;
