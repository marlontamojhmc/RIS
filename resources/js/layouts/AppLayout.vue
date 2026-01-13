<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { Toaster } from 'vue-sonner';
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs"> 
        
    <slot />
    </AppLayout>
</template>
