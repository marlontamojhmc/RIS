<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { watch } from 'vue';
/* -----------------------
Types
----------------------- */
interface UserRole {
    role: string;
    sequence: number | string;
    approver_group_id: number;
}

interface ApplicationItem {
    id: number;
    control_number?: string | null;
    status?: string;
    application?: {
        form_type?: string;
        id: number;
        form_title?: string;
        form_number?: string;
        created_at?: string;
        status?: string;
        user?: { name?: string };
        approver_group_approvers?: {
            role: string;
            status: string;
            updated_at?: string;
            approver?: { id?: number; name?: string };
            approver_id?: number;
        }[];
    } | null;
}

const props = defineProps<{
    userRole: UserRole;
    applicationProps: ApplicationItem | null;
    userId: number;
    sequence: any;
}>();
/* -----------------------
Forms
----------------------- */
const form = useForm({
    form_title: '',
    form_type: '',
    form_id: '',
    application_date: '',
    application_id: 0,
    status: '',
    approved_date: '',
    form_number: '',
    control_number: '',
    locator_name: '',
    approvers: [] as any[],
    user_app_selection: [] as any[],
    // fee_option: [] as any[],
});
watch(
    () => props.applicationProps,
    (app) => {
        if (!app) return;

        // Handle the broadcast structure vs the initial Inertia prop structure
        const source = app.application ? app.application : app;
        console.log('SOURCE', source);
        console.log('Syncing Modal. Status:', source.status);

        form.application_id = source.id;
        form.status = source.status ?? '';
        form.form_title = (source as any).form_title ?? '';
        form.application_date = (source as any)?.created_at ?? '';
        form.approved_date = (source as any)?.updated_at ?? '';
        form.control_number = (source as any)?.control_number ?? '';
        form.locator_name = (source as any)?.user?.name ?? '';
        form.form_number = (source as any)?.form_number ?? '';
        form.form_type = (source as any).form_type ?? '';
        form.form_id = String(source.id);
        form.user_app_selection = (source as any)?.user_app_selection[0] ?? [];
        // form.fee_option =
        //     (source as any)?.user_app_selection[0].fee_option ?? [];

        // The fix: Explicitly update approvers from the broadcast data
        if ((source as any).approver_group_approvers) {
            form.approvers = JSON.parse(
                JSON.stringify((source as any).approver_group_approvers),
            );
            console.log(
                'Signatories updated successfully:',
                form.approvers.length,
            );
        }
    },
    { immediate: true, deep: true },
);
const logs = [
    {
        title: 'Application Submitted',
        message: 'Submitted by Registration Officer',
        status: 'approved',
        date: 'Mar 4, 2026',
    },
    {
        title: 'Finance Review',
        message: 'Waiting for verification',
        status: 'pending',
        date: 'Mar 5, 2026',
    },
    {
        title: 'Final Approval',
        message: 'Awaiting decision',
        status: 'pending',
        date: 'Mar 6, 2026',
    },
    {
        title: 'Final Approval',
        message: 'Awaiting decision',
        status: 'pending',
        date: 'Mar 6, 2026',
    },
    {
        title: 'Final Approval',
        message: 'Awaiting decision',
        status: 'pending',
        date: 'Mar 6, 2026',
    },
];
</script>
<template>
    <div class="flow-root">
        <ul role="list" class="relative ml-4 border-l border-gray-200">
            <li v-for="(log, index) in logs" :key="index" class="mb-8 ml-6">
                <!-- Circle with step number -->
                <span
                    class="absolute -left-4 flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white ring-8 ring-white"
                >
                    {{ index + 1 }}
                </span>

                <!-- Content -->
                <div
                    class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-semibold text-gray-900">
                            {{ log.title }}
                        </h3>
                        <span class="text-xs text-gray-500">
                            {{ log.date }}
                        </span>
                    </div>

                    <p class="mt-1 text-sm text-gray-600">
                        {{ log.message }}
                    </p>

                    <span
                        class="mt-2 inline-block rounded-md px-2 py-1 text-xs"
                        :class="{
                            'bg-green-100 text-green-700':
                                log.status === 'approved',
                            'bg-yellow-100 text-yellow-700':
                                log.status === 'pending',
                            'bg-red-100 text-red-700':
                                log.status === 'rejected',
                        }"
                    >
                        {{ log.status }}
                    </span>
                </div>
            </li>
        </ul>
    </div>
</template>
