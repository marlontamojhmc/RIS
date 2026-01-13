<script setup lang="ts">
import { Avatar, AvatarImage, AvatarFallback } from '@/components/ui/avatar';
import { computed ,ref} from 'vue';
import { AlertCircle } from 'lucide-vue-next';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';



interface Props {
    user: User;
    showEmail?: boolean;
    hasNotification: boolean;
}



const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
   
});


const hasNotification = ref(false);



console.log("hasNotification in UserMenuContent:", hasNotification.value);
console.log('UserInfo props:', props.hasNotification );
console.log('UserInfo user:', props.user );
const emit = defineEmits(['clear-notification']);

const { getInitials } = useInitials();
const showAvatar = computed(() => props.user.avatar && props.user.avatar !== '');

function handleClick() {
    emit('clear-notification');
}
</script>
<template>
    
    <div class="flex items-center gap-2 cursor-pointer" @click="handleClick">
        <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
            <AvatarImage v-if="showAvatar" :src="props.user.avatar!" :alt="user.name" />
            <AvatarFallback class="rounded-lg text-black dark:text-white">{{ getInitials(props.user.name) }}</AvatarFallback>
        </Avatar>

        <div class="flex flex-col">
            <span class="truncate font-medium">{{ props.user.name }}</span>
            <span v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ props.user.email }}</span>
        </div>

        <div class="relative">

            <span v-if="props.hasNotification"
                class="absolute -top-4 -ight- bg-red-600 text-white text-xs w-4 h-4 flex items-center justify-center rounded-full font-bold">!</span>
        </div>
    </div>
</template>
