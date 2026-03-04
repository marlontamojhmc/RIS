<script setup lang="ts">
import ApplicationFormsTable from '@/components/ApplicationFormsTable.vue';
import SezadTimeline from '@/components/sezad/SezadTimeline.vue';
import SezadModalCard from '@/components/SezadModalCard.vue';
import Modal from '@/components/View/Modal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
interface UserRole {
    role: string;
    sequence: number | string;
    approver_group_id: number;
}

interface Application {
    id: number;
    control_number?: string;
    form_title?: string;
    form_number?: string;
    form_type?: string;
    status?: string;
    approver_group_approvers?: any[];
    [key: string]: any;
}

interface PageProps extends Record<string, unknown> {
    name: string;
    quote: { message: string; author: string };
    sidebarOpen: boolean;
    auth: {
        user: {
            id: number;
            name: string;
            email: string;
            email_verified_at: string | null;
            created_at: string;
            updated_at: string;
            details?: any;
            role: UserRole[];
        };
    };
    applications: Application[];
}

const page = usePage<PageProps>();
const props = defineProps<{ applications: Application[] }>();

const userRole = page.props.auth.user.role[0];
const userId = page.props.auth.user.details.user_id;
// console.log('index UserRole', userRole);
// console.log('index UserID', userId);
const sequence = page.props.auth.user.role as UserRole[];
console.log('check', sequence);

const applications = ref<Application[]>(
    props.applications ? [...props.applications] : [],
);
const showRecords = ref(false);
const showTimeline = ref(false);
const selectedApplication = ref<Application | null>(null);

const onOpenRecords = (app: Application) => {
    // Clone to ensure a clean reference for the modal
    selectedApplication.value = JSON.parse(JSON.stringify(app));
    showRecords.value = true;
};
const onOpenTimeline = (app: Application) => {
    // Clone to ensure a clean reference for the modal
    selectedApplication.value = JSON.parse(JSON.stringify(app));
    showTimeline.value = true;
};

const onClose = () => {
    showRecords.value = false;
    showTimeline.value = false;
    // selectedApplication.value = null;
};

onMounted(() => {
    if (!window.Echo) {
        console.error('Echo is not defined. Check your bootstrap.js');
        return;
    }

    window.Echo.channel('applications').listen(
        '.ApplicationUpdated',
        (event: any) => {
            const newAppData = event.application;
            if (!newAppData) return;

            const index = applications.value.findIndex((item: any) => {
                const id = item.application?.id || item.id;
                return Number(id) === Number(newAppData.id);
            });

            if (index !== -1) {
                // Create the updated object
                const updatedItem = {
                    ...applications.value[index],
                    ...newAppData,
                    application: newAppData,
                    // Ensure the list is at the top level for the watcher
                    approver_group_approvers:
                        newAppData.approver_group_approvers,
                };

                // Update the main table
                applications.value.splice(index, 1, updatedItem);

                // SYNC THE MODAL
                if (selectedApplication.value) {
                    const currentId =
                        selectedApplication.value.application?.id ||
                        selectedApplication.value.id;
                    if (Number(currentId) === Number(newAppData.id)) {
                        // Use a fresh object reference to trigger the watcher in SezadModalCard
                        selectedApplication.value = JSON.parse(
                            JSON.stringify(updatedItem),
                        );
                        console.log('MODAL DATA REPLACED - Syncing Approvers');
                    }
                }
            }
        },
    );
});

watch(
    () => props.applications,
    (newApps) => {
        applications.value = [...newApps];
    },
    { deep: true },
);
</script>

<template>
    <AppLayout>
        <div>
            <h1 class="mb-4 text-center text-2xl font-bold">
                {{ userRole?.role }} Dashboard
            </h1>
        </div>

        <div class="p-1.5">
            <ApplicationFormsTable
                :userRole="userRole"
                :applications="applications"
                @onOpenRecords="onOpenRecords"
                @onOpenTimeline="onOpenTimeline"
            />

            <Modal :show="showRecords" @close="onClose" maxWidth="max-w-2xl">
                <SezadModalCard
                    v-if="selectedApplication"
                    :applicationProps="selectedApplication"
                    :userRole="userRole"
                    :userId="userId"
                    :sequence="sequence"
                />
            </Modal>
            <Modal :show="showTimeline" @close="onClose" maxWidth="max-w-2xl">
                <SezadTimeline
                    v-if="selectedApplication"
                    :applicationProps="selectedApplication"
                    :userRole="userRole"
                    :userId="userId"
                    :sequence="sequence"
                />
            </Modal>
        </div>
    </AppLayout>
</template>
