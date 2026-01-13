<script setup lang="ts">
//import UserInfo from '@/components/UserInfo.vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';
import type { User } from '@/types';


const page = usePage();
const user = page.props.auth.user;
const { isMobile, state } = useSidebar();
import { ref, onMounted ,watch} from 'vue';
import * as Ably from 'ably';

const props = defineProps<{
    user: User;
    hasNotification: boolean;
}>();

const emit = defineEmits(["clear-notification"]);



const hasNotification = ref(false);

console.log("hasNotification in NavUser:", hasNotification.value);
function clearNotification() {
    //alert("Notifications cleared");
    hasNotification .value = false;
}
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child >
                    <SidebarMenuButton size="lg">
                        <!-- <UserInfo
                            :user="props.user"
                            :has-notification="props.hasNotification"
                            @clear-notification="clearNotification"
                        /> -->
                    </SidebarMenuButton>
                </DropdownMenuTrigger>

                <DropdownMenuContent align="end" :side-offset="4">
                    <UserMenuContent :user="user"  :has-notification="props.hasNotification"
                             @clear-notification="$emit('clear-notification')" />
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
