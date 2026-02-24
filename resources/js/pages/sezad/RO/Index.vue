<script setup lang="ts">
import ApplicationFormsTable from '@/components/ApplicationFormsTable.vue';
import SezadModalCard from '@/components/SezadModalCard.vue';
import Modal from '@/components/View/Modal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { PageProps as InertiaPageProps } from '@inertiajs/core';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

interface UserRole {
    role: string;
    sequence: number | string;
    approver_group_id: number;
}
interface Application {
    id: number;
    control_number?: string;
    application?: {
        form_title?: string;
        form_number?: string;
        status?: string;
    } | null;
}
interface PageProps extends InertiaPageProps {
    auth: {
        user: InertiaPageProps['auth']['user'] & {
            role: UserRole[];
        };
    };
}
const props = defineProps<{
    applications: Application[];
}>();

const page = usePage<PageProps>();

const userRole = page.props.auth.user.role[0];
const applications = props.applications;
const showModal = ref(false);
const selectedApplication = ref<Application | null>(null);

const onOpenModal = (app: any) => {
    console.log('Selected Application:', app);
    selectedApplication.value = app;

    showModal.value = true;
    console.log(
        'Selected Application after setting:',
        selectedApplication.value,
    );
};
const onClose = () => {
    showModal.value = false;
};
</script>

<template>
    <AppLayout>
        <!-- <AppSidebarLayout> -->
        <div>
            <h1 class="mb-4 text-center text-2xl font-bold">
                Registration Officer
            </h1>
        </div>
        <div class="p-1.5">
            <pre>
                <!-- {{ applications }} -->
            </pre>
            <ApplicationFormsTable
                :userRole="userRole"
                :applications="applications"
                @onOpenModal="onOpenModal"
            />
            <Modal :show="showModal" @close="onClose" maxWidth="max-w-2xl">
                <SezadModalCard
                    :applicationProps="selectedApplication"
                    :userRole="userRole"
                />
            </Modal>
        </div>

        <!-- </AppSidebarLayout> -->
    </AppLayout>
</template>
