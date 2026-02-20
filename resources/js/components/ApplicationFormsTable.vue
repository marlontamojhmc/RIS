<script setup lang="ts">
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Inertia } from '@inertiajs/inertia';
import { useForm, usePage } from '@inertiajs/vue3';
import { Eye } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';
import Modal from './View/Modal.vue';
const props = defineProps({
    userRole: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const applications = computed(() => page.props.applications as any[]);
const toast = useToast();
// refs
const showModal = ref(false);
const openPayment = ref(false);

// forms
const form = useForm({
    form_title: '',
    form_type: '',
    form_id: '',
    application_date: '',
    application_id: '',
    status: '',
    approved_date: '',
    form_number: '',
    control_number: '',
    locator_name: '',
    approvers: [] as any[],
});

const formPayment = useForm({
    application_forms_id: '',
    form_type: '',
    form_number: '',
    form_id: '',
    approver_id: '',
    is_number: '',
    amount: '',
});

const approveForm = useForm({
    user_id: 0,
    application_form_id: '',
    approver_group_id: '',
    sequence: '',
});
// ==================
//     Toggle
// ==================
// toggle payment footer
const openPaymentFooter = (value: boolean) => {
    openPayment.value = value;
};

//Modal
const handleView = (application: any) => {
    const app = application?.application;
    console.log(app);
    if (!app) return;

    const lastApprover = app.approver_group_approvers?.at(-1);
    form.application_id = app.id ?? '';
    form.form_title = app.form_title ?? '';
    form.form_id = app.form_id ?? '';
    form.form_type = app.form_type ?? '';
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
    // openPaymentFooter(false);
};
const submitPayment = () => {
    formPayment.application_forms_id = form.application_id;
    formPayment.form_type = form.form_type;
    formPayment.form_number = form.form_number;
    formPayment.form_id = form.form_id;
    formPayment.approver_id = '';

    formPayment.post('accept-payment', {
        onSuccess: () => {
            toast.success('Payment processed successfully.');
            formPayment.reset();
            openPayment.value = false;

            Inertia.reload({
                only: ['applications'],
                preserveState: true,
            });
        },
        onError: () => {
            toast.error('Something went wrong.');
        },
    });
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
const statusClasses: Record<string, string> = {
    approved: 'bg-green-100 text-green-700',
    pending: 'bg-yellow-100 text-yellow-700',
    draft: 'bg-blue-100 text-blue-700',
    cancelled: 'bg-gray-200 text-gray-700',
    rejected: 'bg-red-100 text-red-700',
};
const normalizeStatus = (status: string) => status?.toLowerCase() ?? '';
// const emit = defineEmits(['view', 'edit', 'delete']);

const onApprove = () => {
    approveForm.user_id = page.props.auth.user.id;
    approveForm.application_form_id = form.application_id;
    approveForm.approver_group_id = props.userRole.approver_group_id;
    approveForm.sequence = props.userRole.sequence;

    approveForm.post('/sezad/approve', {
        onSuccess: () => {
            toast.success('Application approved successfully.');

            Inertia.reload({
                only: ['applications'],
            });
        },
        onError: () => {
            toast.error('Something went wrong.');
        },
    });
};
watch(applications, (newApps) => {
    if (!showModal.value) return;

    const updated = newApps.find(
        (a: any) => a.application.id === form.application_id,
    );

    if (!updated) return;

    const app = updated.application;
    const lastApprover = app.approver_group_approvers?.at(-1);

    form.status = app.status;
    form.approved_date = lastApprover?.updated_at ?? '';
    form.approvers = app.approver_group_approvers ?? [];
});
</script>

<template>
    <div
        class="overflow-x-auto rounded-lg border border-gray-300 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
    >
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-100 dark:bg-gray-800">
                <tr class="text-center">
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        #
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Form Title
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Control Number
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Form Number
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Status
                    </th>
                    <th
                        class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200"
                    >
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody
                v-if="applications.length"
                class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900"
            >
                <tr
                    v-for="(app, index) in applications"
                    :key="app.id"
                    class="text-center transition hover:bg-gray-50 dark:hover:bg-gray-800"
                >
                    <td class="px-4 py-2 align-middle text-sm">
                        {{ index + 1 }}
                    </td>
                    <td
                        class="px-4 py-2 align-middle text-sm text-gray-800 dark:text-gray-100"
                    >
                        {{ app.application?.form_title ?? '-' }}
                    </td>
                    <td
                        class="px-4 py-2 align-middle text-sm text-gray-600 dark:text-gray-300"
                    >
                        {{ app.control_number ?? 'N/A' }}
                    </td>
                    <td
                        class="px-4 py-2 align-middle text-sm text-gray-600 dark:text-gray-300"
                    >
                        {{ app.application?.form_number ?? '—' }}
                    </td>
                    <td
                        class="px-4 py-2 align-middle text-sm text-gray-600 capitalize dark:text-gray-300"
                    >
                        {{ app.application?.status ?? 'N/A' }}
                    </td>
                    <td
                        class="flex items-center justify-center gap-2 px-4 py-2 text-sm"
                    >
                        <button
                            @click="handleView(app)"
                            class="rounded-full p-1 text-blue-600 transition hover:bg-blue-50 hover:text-blue-800 dark:text-blue-400 dark:hover:bg-blue-900/30 dark:hover:text-blue-300"
                            title="View"
                        >
                            <Eye class="h-5 w-5" />
                        </button>
                        <!-- <button
              @click="emit('edit', app)"
              class="text-green-600 hover:text-green-800 p-1 rounded-full hover:bg-green-50 dark:text-green-400 dark:hover:text-green-300 dark:hover:bg-green-900/30 transition"
              title="Edit"
            >
              <Pencil class="w-5 h-5" />
            </button>
           
            <button
              @click="emit('delete', app)"
              class="text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/30 transition"
              title="Delete"
            >
              <Trash2 class="w-5 h-5" />
            </button> -->
                    </td>
                </tr>
            </tbody>

            <tbody v-else>
                <tr>
                    <td
                        colspan="6"
                        class="py-4 text-center text-gray-500 dark:text-gray-400"
                    >
                        No applications found.
                    </td>
                </tr>
            </tbody>
        </table>
        <Modal :show="showModal" @close="onClose" maxWidth="max-w-2xl">
            <Card
                class="relative z-10 max-h-[90vh] w-full overflow-y-auto border-2 border-amber-50"
            >
                <!-- Header -->
                <CardHeader>
                    <div class="flex w-full items-start justify-between">
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
                <CardContent class="space-y-1">
                    <div class="flex flex-row justify-between">
                        <!-- left -->
                        <div>
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
                        </div>
                        <!-- right -->
                        <div v-if="form.approvers.length">
                            <h3 class="text-base font-semibold text-gray-800">
                                Approvers
                            </h3>

                            <ol class="space-y-1">
                                <li
                                    v-for="(a, i) in form.approvers"
                                    :key="i"
                                    class="flex items-center justify-between rounded-lg border bg-gray-50 px-1 py-1"
                                >
                                    <!-- Left Section -->
                                    <div class="flex flex-col">
                                        <span
                                            class="font-semibold text-gray-900"
                                        >
                                            {{ a.approver?.name }}
                                        </span>

                                        <span class="text-sm text-gray-500">
                                            {{ a.role }}
                                        </span>
                                    </div>

                                    <!-- Right Section (Status Badge) -->
                                    <span
                                        class="rounded-full px-3 py-1 text-xs font-medium capitalize"
                                        :class="
                                            statusClasses[
                                                normalizeStatus(a.status)
                                            ] || 'bg-gray-100 text-gray-600'
                                        "
                                    >
                                        {{ a.status }}
                                    </span>
                                </li>
                            </ol>
                        </div>
                    </div>

                    <div v-show="userRole.role == 'Finance'">
                        <div class="flex gap-2">
                            <button
                                v-show="!openPayment"
                                type="button"
                                @click="openPaymentFooter(true)"
                                class="rounded-md bg-primary px-4 py-2 text-primary-foreground transition hover:bg-primary/90"
                            >
                                Proceed Payment
                            </button>

                            <button
                                v-show="openPayment"
                                type="button"
                                @click="openPaymentFooter(false)"
                                class="rounded-md bg-destructive text-destructive-foreground transition hover:bg-destructive/90"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>
                    <div v-show="userRole.role == 'Registration Officer'">
                        <!-- {{ page }} -->
                        <button @click="onApprove">Approve</button>
                    </div>
                </CardContent>

                <!-- Footer -->
                <CardFooter v-show="openPayment" class="flex flex-row">
                    <div class="flex flex-col">
                        <div>
                            <Input
                                type="number"
                                name="text"
                                v-model="formPayment.is_number"
                                placeholder="Enter IS Number"
                            />
                        </div>
                        <div>
                            <Input
                                type="number"
                                name="amount"
                                v-model="formPayment.amount"
                                placeholder="Amount"
                            />
                        </div>
                    </div>
                    <div class="flex flex-col">
                        <!-- <div>
                                <Calendar v-model="formPayment.payment_date" />
                            </div> -->
                        <div>
                            <button
                                class="rounded-md bg-primary px-4 py-2 text-white transition hover:bg-primary/90"
                                @click="submitPayment"
                            >
                                Accept Button
                            </button>
                        </div>
                    </div>
                </CardFooter>
            </Card>
        </Modal>
    </div>
</template>
