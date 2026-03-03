<script setup lang="ts">
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router, usePage } from '@inertiajs/vue3';
import { BellRing, LogOut, Settings } from 'lucide-vue-next';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const notificationsUrl = route('sezad.notifications');
const props = defineProps<{
    user: User;
    hasNotification: boolean;
}>();

const hasNotification = ref(props.hasNotification);
const emit = defineEmits<{
    (e: 'clear-notification'): void;
}>();

function clearNotification() {
    emit('clear-notification');
    hasNotification.value = false;
}

const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <!-- User info can go here -->
        </div>
    </DropdownMenuLabel>

    <DropdownMenuSeparator />

    <DropdownMenuGroup>
        <!-- Settings -->
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="edit()" prefetch as="button">
                <Settings class="mr-2 h-4 w-4" />
                Settings
            </Link>
        </DropdownMenuItem>

        <DropdownMenuSeparator />

        <!-- Notifications -->
        <DropdownMenuItem :as-child="true">
            <Link
                :href="notificationsUrl"
                class="relative flex w-full items-center px-2 py-1"
            >
                <BellRing class="mr-2 h-4 w-4" />
                <span>Notifications</span>
                <span
                    v-if="page.props.auth.user?.count_notification > 0"
                    class="absolute top-4 right-4 mr-4 inline-flex translate-x-1/2 -translate-y-1/2 transform items-center justify-center rounded-full bg-red-500 px-2 py-1 text-xs leading-none font-bold text-white"
                >
                    {{ page.props.auth.user.count_notification }}
                </span>
                <span
                    v-if="hasNotification"
                    class="absolute top-2 right-2 h-4 w-4 rounded-full bg-red-500"
                ></span>
            </Link>
        </DropdownMenuItem>
    </DropdownMenuGroup>

    <DropdownMenuSeparator />

    <!-- Logout -->
    <DropdownMenuItem :as-child="true">
        <Link
            class="block w-full"
            :href="logout()"
            @click.prevent="handleLogout"
            as="button"
            data-test="logout-button"
        >
            <LogOut class="mr-2 h-4 w-4" />
            Log out
        </Link>
    </DropdownMenuItem>
</template>
