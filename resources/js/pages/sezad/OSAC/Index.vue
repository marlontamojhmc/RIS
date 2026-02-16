<script setup lang="ts">
import Modal from '@/components/View/Modal.vue';
import OsacAppSidebarLayout from '@/layouts/Osac/OsacAppSidebarLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import osacApplicationTable from './OsacComponents/osacApplicationTable.vue';
const props = defineProps({
    applications: {
        type: Object,
        required: true,
    },
});
const form = useForm({
    form_title: '',
    application_date: '',
    status: '',
    approved_date: '',
    form_number: '',
    control_number: '',
    locator_name: '',
    option: '',
    uploads: '',
    articles: [] as any[],
    approvers: [] as any[],
});

const showModal = ref(false);
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
    form.articles = app.article_details ?? [];
    form.option = app.selections ?? '';
    form.uploads = app.uploads ?? '';
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
    <OsacAppSidebarLayout>
        <div>
            <h1 class="mb-4 text-center text-2xl font-bold">OSAC Dashboard</h1>

            <osacApplicationTable
                :applications="props.applications"
                @view="handleView"
                @edit="handleEdit"
                @delete="handleDelete"
            />
        </div>
    </OsacAppSidebarLayout>
    <Modal :show="showModal" @close="onClose">
        <Card class="relative z-10 w-full max-w-lg">
            <!-- Header -->
            <CardHeader class="space-y-2">
                <!-- Title -->
                <h3 class="text-lg font-semibold">
                    {{ form.form_title }}
                </h3>

                <!-- Status Below -->
                <p class="text-sm text-gray-600">
                    Status:<span class="text-green-600">{{ form.status }}</span>
                </p>
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

                <div class="mt-6">
                    <h3 class="mb-3 text-base font-semibold">Approvers</h3>

                    <div class="divide-y rounded-md border">
                        <div
                            v-for="(a, i) in form.approvers"
                            :key="i"
                            class="flex items-center gap-3 px-4 py-2 text-sm"
                        >
                            <!-- Number Circle -->
                            <div
                                class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-200 text-xs font-semibold"
                            >
                                {{ i + 1 }}
                            </div>

                            <span>
                                {{ a.approver?.name ?? 'N/A' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div v-if="form.articles.length">
                    <div class="mt-4 overflow-x-auto">
                        <table
                            class="min-w-full border border-gray-200 text-sm"
                        >
                            <!-- Table Head -->
                            <thead class="bg-gray-100 text-left">
                                <tr>
                                    <th class="border px-4 py-2">
                                        Marks & Number
                                    </th>
                                    <th class="border px-4 py-2">Qty</th>
                                    <th class="border px-4 py-2">
                                        Description
                                    </th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody>
                                <tr
                                    v-for="item in form.articles"
                                    :key="item.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="border px-4 py-2">
                                        {{ item.marks_and_number }}
                                    </td>

                                    <td class="border px-4 py-2 text-center">
                                        {{ item.qty }}
                                    </td>

                                    <td class="border px-4 py-2">
                                        {{
                                            item.detailed_description_of_article
                                        }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-show="form.option">
                        {{ form.option }}
                    </div>
                    <div v-show="form.uploads">
                        {{ form.uploads }}
                    </div>
                </div>
            </CardContent>
        </Card>
    </Modal>
</template>
