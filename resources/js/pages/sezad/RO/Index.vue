<script setup lang="ts">
import ApplicationFormsTable from '@/components/ApplicationFormsTable.vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();

const userRole = page.props.auth.user.role[0];1
</script>

<template>
 

    <ApplicationFormsTable :userRole="userRole" />
    <!-- </FinanceAppSidebarLayout> -->
    <!-- </AppLayout> -->
</template>
