<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

import Modal from '@/components/View/Modal.vue';
import FinanceAppSidebarLayout from '@/layouts/Finance/FinanceAppsidebarLayout.vue';
import FinanceApplicationTable from './FinanceComponents/FinanceApplicationTable.vue';

import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

const props = defineProps<{
    applications: Record<string, any>;
}>();

const showModal = ref(false);

const form = useForm({
    form_title: '',
    application_date: '',
    status: '',
    approved_date: '',
    form_number: '',
    control_number: '',
    locator_name: '',
    approvers: [] as any[],
});

const handleView = (application: any) => {
    const app = application?.application;
    console.log(application);
    if (!app) return;

    const lastApprover = app.approver_group_approvers?.at(-1);

    form.form_title = app.form_title ?? '';
    form.application_date = application.application.created_at ?? '';
    form.status = app.status ?? '';
    form.approved_date = lastApprover?.updated_at ?? '';
    form.form_number = app.form_number ?? '';
    form.control_number = app.control_number ?? '';
    form.locator_name = app.user?.name ?? '';
    form.approvers = app.approver_group_approvers ?? [];

    showModal.value = true;
    console.log(form);
};

const onClose = () => {
    showModal.value = false;
};

const handleEdit = (application: any) => {
    console.log('Edit:', application);
};

const handleDelete = (application: any) => {
    console.log('Delete:', application);
};
const formatDate = (value: string | null) => {
    if (!value) return '';

    const date = new Date(value);

    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: '2-digit',
    }).format(date);
};
</script>

<template>
    <FinanceAppSidebarLayout>
        <div>
            <h1 class="mb-4 text-center text-2xl font-bold">
                Finance Dashboard
            </h1>

            <FinanceApplicationTable
                :applications="props.applications"
                @view="handleView"
                @edit="handleEdit"
                @delete="handleDelete"
            />

            <Modal :show="showModal" @close="onClose">
                <Card class="relative z-10 w-full max-w-lg">
                    <!-- Header -->
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div>
                                <CardTitle>
                                    {{ form.form_title }}
                                </CardTitle>

                                <CardDescription>
                                    Status: {{ form.status }}
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>

                    <!-- Content -->
                    <CardContent class="space-y-2">
                        <p>
                            Application Date:
                            {{ formatDate(form.application_date) }}
                        </p>
                        <p>
                            Approved Date:
                            {{ formatDate(form.approved_date) }}
                        </p>
                        <p>Control #: {{ form.control_number }}</p>
                        <p>Locator: {{ form.locator_name }}</p>

                        <div v-if="form.approvers.length">
                            <h3 class="mt-4 font-semibold">Approvers:</h3>
                            <ul class="list-disc pl-5">
                                <li v-for="(a, i) in form.approvers" :key="i">
                                    {{ a.approver?.name }}
                                </li>
                            </ul>
                        </div>
                    </CardContent>

                    <!-- Footer -->
                    <!-- <CardFooter>
                        <div class="flex w-full justify-end">
                            <button
                                class="rounded bg-blue-600 px-4 py-2 text-white"
                                @click="onClose"
                            >
                                Close
                            </button>
                        </div>
                    </CardFooter> -->
                </Card>
            </Modal>
        </div>
    </FinanceAppSidebarLayout>
</template>
