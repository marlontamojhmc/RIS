<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    unread: { type: Array, required: true },
    all: { type: Array, required: true },
    count_notification: { type: Number, required: true },
});

// Local reactive state
const unreadNotifications = ref([...props.unread]);
const allNotifications = ref([...props.all]);

// Mark a single notification as read
async function markAsRead(notificationId) {
    try {
        const res = await fetch(`/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
                Accept: 'application/json',
            },
        });

        const data = await res.json();
        console.log(data.message);

        const index = unreadNotifications.value.findIndex(
            (n) => n.id === notificationId,
        );
        if (index !== -1) {
            const [readNotification] = unreadNotifications.value.splice(
                index,
                1,
            );
            if (!allNotifications.value.find((n) => n.id === notificationId)) {
                allNotifications.value.unshift(readNotification);
            }
        }
    } catch (err) {
        console.error('Error marking notification as read:', err);
    }
}
</script>

<template>
    <AppLayout class="p-4">
        <h2 class="mb-2 text-xl font-semibold">Unread Notifications</h2>
        <table class="mb-6 min-w-full border border-gray-200">
            <thead>
                <tr class="bg-gray-100">
                    <th class="p-2 text-left">Message</th>
                    <th class="p-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    v-for="n in unreadNotifications"
                    :key="n.id"
                    class="cursor-pointer hover:bg-gray-50"
                >
                    <td class="p-2 text-green-500">{{ n.data.message }}</td>
                    <td class="p-2">
                        <button
                            @click="markAsRead(n.id)"
                            class="rounded bg-green-500 px-3 py-1 text-white hover:bg-green-600"
                        >
                            Mark as Read
                        </button>
                    </td>
                </tr>
                <tr v-if="unreadNotifications.length === 0">
                    <td colspan="2" class="p-2 text-center text-gray-500">
                        No unread notifications
                    </td>
                </tr>
            </tbody>
        </table>

        <h2 class="mb-2 text-xl font-semibold">Read Notifications</h2>
        <ul class="divide-y divide-gray-100 rounded border border-gray-200">
            <li
                v-for="n in allNotifications"
                :key="!n.read_at"
                class="flex items-center justify-between p-2 text-red-500 hover:bg-gray-50"
            >
                <span>
                    {{ n.data.message }}
                    <span v-if="n.read_at" class="ml-2 font-bold text-red-500"
                        >•</span
                    >
                </span>
            </li>
            <li
                v-if="allNotifications.length === 0"
                class="p-2 text-center text-gray-500"
            >
                No notifications
            </li>
        </ul>
        <h2 class="mb-2 text-xl font-semibold">All Notifications</h2>
        <ul class="divide-y divide-gray-100 rounded border border-gray-200">
            <li
                v-for="n in allNotifications"
                :key="n.id"
                class="flex items-center justify-between p-2 hover:bg-gray-50"
            >
                <span>
                    {{ n.data.message }}
                    <span class="ml-2 font-bold text-red-500">•</span>
                </span>
            </li>
            <li
                v-if="allNotifications.length === 0"
                class="p-2 text-center text-gray-500"
            >
                No notifications
            </li>
        </ul>
    </AppLayout>
</template>
