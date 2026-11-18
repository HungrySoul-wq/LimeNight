<template>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="34"
        height="34"
        viewBox="0 0 24 24"
        fill="none"
        stroke="#B7FF00"
        stroke-width="1.6"
        stroke-linecap="round"
        stroke-linejoin="round"
    >
        <template v-if="name === 'computers'">
            <rect x="2" y="3" width="20" height="14" rx="2"/>
            <path d="M8 21h8M12 17v4"/>
        </template>

        <template v-else-if="name === 'zones'">
            <path d="M6 11h4M8 9v4M15 10h.01M18 12h.01"/>
            <path d="M7 5h10a4 4 0 0 1 3.8 2.8l1 3.2a3 3 0 0 1-4.7 3.3l-2.1-1.6H9l-2.1 1.6a3 3 0 0 1-4.7-3.3l1-3.2A4 4 0 0 1 7 5Z"/>
        </template>

        <template v-else-if="name === 'rooms'">
            <path d="M3 21V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v16"/>
            <path d="M3 21h18M9 21v-5h6v5M8 8h.01M16 8h.01"/>
        </template>

        <template v-else-if="name === 'statistics'">
            <path d="M3 3v18h18"/>
            <path d="m7 14 4-4 4 3 6-7"/>
        </template>

        <template v-else-if="name === 'users'">
            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
            <circle cx="9" cy="7" r="4"/>
            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
        </template>

        <template v-else-if="name === 'bookings'">
            <rect x="3" y="5" width="18" height="16" rx="2"/>
            <path d="M16 3v4M8 3v4M3 11h18"/>
            <path d="m9 16 2 2 4-4"/>
        </template>

        <template v-else-if="name === 'prices'">
            <rect x="2" y="5" width="20" height="14" rx="2"/>
            <path d="M2 10h20M6 15h3"/>
            <circle cx="16" cy="14" r="2"/>
        </template>
    </svg>
</template>

<script setup>
defineProps({
    name: {
        type: String,
        required: true,
    },
});
</script>
