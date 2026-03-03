<script setup lang="ts">
import ApplicationFormsTable from '@/components/ApplicationFormsTable.vue';
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

// Fixed Interface: 'control_number' is string | undefined to match the plugin's expectation
interface Application {
    id: number;
    control_number?: string;
    form_title?: string;
    form_number?: string;
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
            role: UserRole[];
        };
    };
    applications: Application[];
}

const page = usePage<PageProps>();
const props = defineProps<{ applications: Application[] }>();

const userRole = page.props.auth.user.role[0];
const applications = ref<Application[]>([...props.applications]);
const showModal = ref(false);
const selectedApplication = ref<Application | null>(null);

const onOpenModal = (app: Application) => {
    selectedApplication.value = app;
    showModal.value = true;
};

const onClose = () => {
    showModal.value = false;
};

onMounted(() => {
    if (!window.Echo) return;

    window.Echo.channel('applications').listen(
        '.ApplicationUpdated',
        (event: any) => {
            // Get the application from the event
            const updatedApp = event.application ? event.application : event;
            const targetId = Number(updatedApp.id);

            console.log('Searching for targetId:', targetId);
            console.log(
                'Current IDs in table:',
                applications.value.map((a) => a.id),
            );

            // IMPROVED SEARCH: Check root ID and nested ID
            const index = applications.value.findIndex((a) => {
                const rootId = a.id ? Number(a.id) : null;
                const nestedId = a.application?.id
                    ? Number(a.application.id)
                    : null;
                return rootId === targetId || nestedId === targetId;
            });

            if (index !== -1) {
                console.log('Match found at index:', index);

                // Update the table
                applications.value.splice(index, 1, updatedApp);

                // Force Update Modal
                if (selectedApplication.value) {
                    const selectedId =
                        selectedApplication.value.id ||
                        selectedApplication.value.application?.id;
                    if (Number(selectedId) === targetId) {
                        selectedApplication.value = { ...updatedApp }; // Spread to trigger deep reactivity
                    }
                }
            } else {
                console.warn(
                    `ID ${targetId} still not found in current table rows.`,
                );
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
                Registration Officer Dashboard
            </h1>
        </div>

        <div class="p-1.5">
            <ApplicationFormsTable
                :userRole="userRole"
                :applications="applications"
                @onOpenModal="onOpenModal"
            />

            <Modal :show="showModal" @close="onClose" maxWidth="max-w-2xl">
                <SezadModalCard
                    v-if="selectedApplication"
                    :applicationProps="selectedApplication"
                    :userRole="userRole"
                />
            </Modal>
        </div>
    </AppLayout>
</template>
