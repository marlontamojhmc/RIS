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
import Modal from '@/components/View/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import FinanceApplicationTable from './FinanceComponents/FinanceApplicationTable.vue';
import FinanceAppsidebarLayout from '@/layouts/Finance/FinanceAppsidebarLayout.vue';
import { useToast } from 'vue-toastification';
const toast = useToast();
const props = defineProps<{
    applications: Record<string, any>;
}>();

const showModal = ref(false);
const openPayment = ref(false);

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

const onClose = () => {
    showModal.value = false;
    openPaymentFooter(false);
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
const openPaymentFooter = (value: boolean) => {
    openPayment.value = value;
};

const statusClasses: Record<string, string> = {
    approved: 'bg-green-100 text-green-700',
    pending: 'bg-yellow-100 text-yellow-700',
    draft: 'bg-blue-100 text-blue-700',
    cancelled: 'bg-gray-200 text-gray-700',
    rejected: 'bg-red-100 text-red-700',
};
const normalizeStatus = (status: string) => status?.toLowerCase() ?? '';

// Submits
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

const submitPayment = () => {
    formPayment.application_forms_id = form.application_id;
    formPayment.form_type = form.form_type;
    formPayment.form_number = form.form_number;
    formPayment.form_id = form.form_id;
    formPayment.approver_id = '';

    console.log(formPayment);
    // formPayment.post('accept-payment');

    formPayment.post('accept-payment', {
        onSuccess: () => {
            toast.success('Payment processed successfully.');
            formPayment.reset();
            openPayment.value = false;
            const updated = props.applications.find(
                (a: any) => a.application.id === form.application_id,
            );

            if (!updated) return;

            const app = updated.application;
            const lastApprover = app.approver_group_approvers?.at(-1);

            form.status = app.status;
            form.approved_date = lastApprover?.updated_at ?? '';
            form.approvers = app.approver_group_approvers ?? [];
        },
        onError: () => {
            toast.error('Something went wrong.');
        },
    });
};
</script>

<template>
    {{}}
    <FinanceAppsidebarLayout>
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
                                <h3
                                    class="text-base font-semibold text-gray-800"
                                >
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
    </FinanceAppsidebarLayout>
</template>
