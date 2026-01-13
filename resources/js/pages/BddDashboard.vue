<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { bddDashboard, dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'BDD Dashboard',
        href: bddDashboard().url,
    },
];
</script>

<template>
    <Head title="BDD Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4"
        >
            
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border"
            >
                <PlaceholderPattern /> 
                <h2>BDD Dashboard</h2>
            </div>
        </div>
    </AppLayout>
</template>
