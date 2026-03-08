<script setup lang="ts">
//import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { logout } from '@/routes';
import { edit } from '@/routes/profile';
import type { User } from '@/types';
import { Link, router } from '@inertiajs/vue3';
import { BellRing, LogOut, Settings } from 'lucide-vue-next';
import { ref } from 'vue';

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
console.log('hasNotification in UserMenuContent:', hasNotification.value);
const handleLogout = () => {
    router.flushAll();
};
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <!-- <UserInfo :user="props.user" :show-email="true" :has-notification=false @clear-notification="clearNotification" /> -->
        </div>
    </DropdownMenuLabel>

    <DropdownMenuSeparator />

    <DropdownMenuGroup>
        <!-- Settings -->
        <DropdownMenuItem :as-child="true">
            <Link class="block w-full" :href="edit()" prefetch as="button">
                <Settings class="mr-2 h-4 w-4 text-black dark:text-gray-400" />
                <p class="text-black dark:text-gray-400">Settings</p>
            </Link>
        </DropdownMenuItem>

        <DropdownMenuSeparator />

        <!-- Notifications -->
        <DropdownMenuItem :as-child="true" @click="emit('clear-notification')">
            <Link
                class="relative flex w-full items-center px-2 py-1"
                prefetch
                @click="clearNotification"
            >
                <BellRing class="mr-2 h-4 w-4" />
                <span class="text-black dark:text-gray-400">Notifications</span>
                <span
                    v-if="props.hasNotification"
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
            @click="handleLogout"
            as="button"
            data-test="logout-button"
        >
            <LogOut class="mr-2 h-4 w-4" />
            <p class="text-black dark:text-gray-400">Log out</p>
        </Link>
    </DropdownMenuItem>
</template>
