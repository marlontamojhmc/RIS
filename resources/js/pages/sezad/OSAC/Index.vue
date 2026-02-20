<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { CardFooter } from '@/components/ui/card';
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
    file_path: '',
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
const showRemark = ref(false);
const handleView = (application: any) => {
    const app = application?.application;

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
};
const formApprove = useForm({
    app_id: '',
    approver_group_id: '',
    approver_sequence: '',
});
const formReject = useForm({
    app_id: '',
    remark: '',
    approver_group_id: '',
    approver_sequence: '',
});
const HandleRerurn = () => {
    showRemark.value = !showRemark.value;
};
const HandleApprove = (app: any) => {
    let applicationApprove = app;
    formApprove.app_id = applicationApprove.approvers[0].application_form_id;
    formApprove.approver_group_id =
        applicationApprove.approvers[0].approver_group_id;
    formApprove.approver_sequence = applicationApprove.approvers[0].sequence;
    formApprove.post('osac/approve');
};
const HandleReject = (app: any) => {
    let applicationReject = app;
    //console.log(applicationReject.approvers[0].approver_group_id);
    formReject.app_id = applicationReject.approvers[0].application_form_id;
    formReject.approver_sequence = applicationReject.approvers[0].sequence;
    formReject.remark = 'Rejecting this application';
    formReject.approver_group_id =
        applicationReject.approvers[0].approver_group_id;

    formReject.post('/sezad/osac/return', {
        onSuccess: (page) => {
            console.log('Rejected successfully');
        },
        onError: (errors) => {
            console.log(errors);
        },
        onFinish: () => {
            console.log('Request finished');
        },
    });

    console.log(applicationReject);
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
const onClose = () => {
    showModal.value = false;
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
            <Modal :show="showModal" @close="onClose" max-width="max-w-2xl">
                <Card
                    class="max-h-[85vh] w-full overflow-hidden border border-amber-500"
                >
                    <!-- Header -->
                    <CardHeader class="space-y-1">
                        <h3 class="text-lg font-semibold">
                            {{ form?.form_title }}
                        </h3>

                        <p class="text-sm text-gray-600">
                            Status:
                            <span class="font-medium text-green-600">
                                {{ form?.status }}
                            </span>
                        </p>
                    </CardHeader>

                    <!-- Content -->
                    <CardContent class="space-y-3 text-sm">
                        <!-- Basic Info -->
                        <div class="space-y-1">
                            <p>
                                Application Date:
                                {{ formatDate(form?.application_date) }}
                            </p>
                            <p>
                                Approved Date:
                                {{ formatDate(form?.approved_date) }}
                            </p>
                            <p>Control #: {{ form?.control_number }}</p>
                            <p>Locator: {{ form?.locator_name }}</p>
                        </div>

                        <!-- Approvers -->
                        <div class="mt-6">
                            <h3 class="mb-3 text-base font-semibold">
                                Approvers
                            </h3>

                            <div class="divide-y rounded-md border">
                                <div
                                    v-for="(a, i) in form?.approvers ?? []"
                                    :key="i"
                                    class="flex items-center gap-3 px-4 py-2"
                                >
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

                        <!-- Articles -->
                        <h3 class="mb-3 text-base font-semibold">
                            Article Details:
                        </h3>
                        <hr />
                        <div v-if="form?.articles?.length" class="mt-6">
                            <div class="overflow-x-auto">
                                <table
                                    class="min-w-full border border-gray-200 text-sm"
                                >
                                    <thead class="bg-gray-100 text-left">
                                        <tr>
                                            <th class="border px-4 py-2">
                                                Marks & Number
                                            </th>
                                            <th
                                                class="border px-4 py-2 text-center"
                                            >
                                                Qty
                                            </th>
                                            <th class="border px-4 py-2">
                                                Description
                                            </th>
                                            <th class="border px-4 py-2">
                                                Amount
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <tr
                                            v-for="item in form.articles"
                                            :key="item.id"
                                            class="hover:bg-gray-50"
                                        >
                                            <td class="border px-4 py-2">
                                                {{ item.marks_and_number }}
                                            </td>

                                            <td
                                                class="border px-4 py-2 text-center"
                                            >
                                                {{ item.qty }}
                                            </td>
                                            <td
                                                class="border px-4 py-2 text-center"
                                            >
                                                {{
                                                    item.detailed_description_of_article
                                                }}
                                            </td>

                                            <td class="border px-4 py-3">
                                                {{ item.Price }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Debug Option -->
                            <h3 class="mb-3 text-base font-semibold">
                                Selected Option:
                            </h3>

                            <!-- No option selected -->
                            <div
                                v-if="!form?.option || form.option.length === 0"
                                class="mt-4"
                            >
                                No Selected Option
                            </div>

                            <!-- Display all selected options -->
                            <div v-else class="mt-4 space-y-3">
                                <div
                                    v-for="opt in form.option"
                                    :key="opt.id"
                                    class="rounded border p-3"
                                >
                                    <p>
                                        <span class="font-semibold"
                                            >Option ID:</span
                                        >
                                        {{ opt.option_id }}
                                    </p>
                                    <p>
                                        <span class="font-semibold"
                                            >Price:</span
                                        >
                                        ₱{{ opt.amount }}
                                    </p>
                                    <p>
                                        <span class="font-semibold"
                                            >Selected At:</span
                                        >
                                        {{ formatDate(opt.selected_at) }}
                                    </p>
                                    <p>
                                        <span class="font-semibold"
                                            >Expired At:</span
                                        >
                                        {{
                                            opt.Expired_at
                                                ? formatDate(opt.Expired_at)
                                                : 'No Expiration'
                                        }}
                                    </p>
                                </div>
                            </div>
                            <h3 class="mb-3 text-base font-semibold">
                                Supporting Documents:
                            </h3>
                            <hr />
                            <div v-if="form?.uploads?.length" class="mt-4">
                                <a
                                    :href="`/storage/${form.uploads[0].file_path}`"
                                    target="_blank"
                                    rel="noopener"
                                    class="inline-block"
                                >
                                    <img
                                        class="rounded border transition hover:scale-105 hover:shadow-md"
                                        width="120"
                                        height="120"
                                        :src="`/storage/${form.uploads[0].file_path}`"
                                        alt="Uploaded file"
                                    />
                                </a>
                            </div>
                        </div>
                        <CardFooter>
                            <Button @click="HandleApprove(form)">
                                Approve
                            </Button>
                            <Button @click="HandleReject(form)">Return</Button>
                            <Button
                                class="bg-color-red-600 text-black-500"
                                @click="HandleRerurn"
                                >Reject</Button
                            >
                            <textarea
                                v-show="showRemark"
                                name=""
                                id=""
                            ></textarea>
                        </CardFooter>
                    </CardContent>
                </Card>
            </Modal>
        </div>
    </OsacAppSidebarLayout>
</template>
