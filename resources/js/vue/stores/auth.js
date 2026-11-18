import { defineStore } from 'pinia';
import api from '../api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        token: localStorage.getItem('limenight_token'),
        user: JSON.parse(localStorage.getItem('limenight_user') || 'null'),
        loading: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => ['admin', 'owner'].includes(state.user?.role),
    },

    actions: {
        async login(credentials) {
            this.loading = true;

            try {
                const response = await api.post('/login', credentials);

                this.token = response.data.access_token;
                this.user = response.data.user;

                localStorage.setItem('limenight_token', this.token);
                localStorage.setItem('limenight_user', JSON.stringify(this.user));

                return true;
            } finally {
                this.loading = false;
            }
        },

        async register(data) {
            this.loading = true;

            try {
                const response = await api.post('/register', data);

                this.token = response.data.access_token;
                this.user = response.data.user;

                localStorage.setItem('limenight_token', this.token);
                localStorage.setItem('limenight_user', JSON.stringify(this.user));

                return true;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            try {
                if (this.token) {
                    await api.post('/logout');
                }
            } finally {
                this.token = null;
                this.user = null;

                localStorage.removeItem('limenight_token');
                localStorage.removeItem('limenight_user');
            }
        },
    },
});
