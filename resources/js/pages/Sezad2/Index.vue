<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
const props = defineProps({
    applications: { type: Object, required: true },
    request: { type: Number, required: true },
    status: { type: String, required: true },
});
</script>
<template>
    <AppLayout>
        sezad
        <Link :href="route('test.index.sezad', 45)">test</Link>
        {{ props.status }}
        {{ props.request }}
    </AppLayout>
</template>
