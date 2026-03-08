<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/* Inertia user */
const page = usePage();
const user = computed<User | null>(() => page.props.auth?.user ?? null);

/* Props */
interface Props {
    showEmail?: boolean;
    hasNotification?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
    hasNotification: false,
});

/* Emits */
const emit = defineEmits<{
    (e: 'clear-notification'): void;
}>();

/* Helpers */
const { getInitials } = useInitials();

const showAvatar = computed(() => Boolean(user.value?.avatar));

function handleClick() {
    emit('clear-notification');
}
</script>

<template>
    <div
        v-if="user"
        class="flex cursor-pointer items-center gap-3 select-none"
        @click="handleClick"
    >
        <!-- Avatar -->
        <Avatar class="h-9 w-9 rounded-lg">
            <AvatarImage
                v-if="showAvatar"
                :src="user.avatar"
                :alt="user.name"
            />
            <AvatarFallback
                class="rounded-lg border-2 text-sm font-medium text-black dark:border-gray-400 dark:text-gray-400"
            >
                {{ getInitials(user.name) }}
            </AvatarFallback>
        </Avatar>

        <!-- User Info -->
        <div class="flex flex-col leading-tight">
            <span
                class="max-w-[160px] truncate font-medium text-black dark:text-gray-400"
            >
                {{ user.name }}
            </span>
            <span
                v-if="showEmail"
                class="max-w-[160px] truncate text-xs text-muted-foreground"
            >
                {{ user.email }}
            </span>
        </div>

        <!-- Notification Badge -->
        <span
            v-if="props.hasNotification"
            class="ml-auto flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[10px] font-bold text-white"
        >
            !
        </span>
    </div>
</template>
