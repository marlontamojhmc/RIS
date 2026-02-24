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
import { computed, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

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

    application?: {
        form_title?: string;
        form_number?: string;
        form_type?: string;
        created_at?: string;
        status?: string;

        user?: {
            name?: string;
        };

        approver_group_approvers?: {
            role: string;
            status: string;
            updated_at?: string;
            approver?: {
                name?: string;
            };
        }[];
    } | null;
}

/* -----------------------
Props (Typed Properly)
----------------------- */
const props = defineProps<{
    userRole: UserRole;
    applicationProps: ApplicationItem | null;
}>();
/* -----------------------
Page
----------------------- */
const page = usePage();

/* -----------------------
Applications from parent
----------------------- */
const application = computed(() => props.applicationProps);

const toast = useToast();

/* -----------------------
Refs
----------------------- */
const showModal = ref(false);
const openPayment = ref(false);
// const applications = datas.value;
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
    approver_group_id: 0,
    sequence: 0,
});

const openPaymentFooter = (value: boolean) => {
    openPayment.value = value;
};

const onClose = () => {
    showModal.value = false;
};

const submitPayment = () => {
    formPayment.application_forms_id = form.application_id;
    formPayment.form_type = form.form_type;
    formPayment.form_number = form.form_number;
    formPayment.form_id = form.form_id;

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

const onApprove = () => {
    approveForm.user_id = page.props.auth.user.id;
    approveForm.application_form_id = form.application_id;
    approveForm.approver_group_id = props.userRole.approver_group_id;
    approveForm.sequence = Number(props.userRole.sequence);

    approveForm.post('/sezad/approve', {
        onSuccess: () => {
            toast.success('Application approved successfully.');
            Inertia.reload({
                only: ['applications'],
                preserveState: true,
                preserveScroll: true,
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

/* -----------------------
Watcher
----------------------- */
watch(
    () => props.applicationProps,
    (app) => {
        if (!app || !app.application) return;

        form.application_id = app.id;
        form.control_number = app.control_number ?? '';
        form.form_title = app.application.form_title ?? '';
        form.form_number = app.application.form_number ?? '';
        form.status = app.application.status ?? '';
        form.application_date = app.application.created_at ?? '';
        form.locator_name = app.application.user?.name ?? '';
        form.approvers = app.application.approver_group_approvers ?? [];
        form.form_type = app.application.form_type ?? '';
        form.form_id = app.application?.id ?? '';
        const last = app.application.approver_group_approvers?.at(-1);
        form.approved_date = last?.updated_at ?? '';
    },
    { immediate: true },
);
console.log(application);
</script>

<template>
    <Card
        class="relative z-10 max-h-[90vh] w-full overflow-y-auto border-2 border-amber-50"
    >
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
                                <span class="font-semibold text-gray-900">
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
                                    statusClasses[normalizeStatus(a.status)] ||
                                    'bg-gray-100 text-gray-600'
                                "
                            >
                                {{ a.status }}
                            </span>
                        </li>
                    </ol>
                </div>
            </div>

            <div v-for="(items, index) in form.approvers" :key="index">
                <div
                    v-if="
                        items.role == userRole.role &&
                        form.approvers[Number(userRole.sequence) - 1]?.status ==
                            'Approved'
                    "
                >
                    <!-- Finance -->
                    <div
                        v-if="
                            userRole.role == 'Finance' &&
                            items.status != 'Approved'
                        "
                    >
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
                    <!-- Other Signatoriesd -->
                    <div
                        v-show="
                            userRole.role != 'Finance' &&
                            items.role == userRole.role &&
                            form.approvers[Number(userRole.sequence) - 1]
                                .status == 'Approved' &&
                            items.status == 'Pending'
                        "
                    >
                        <!-- {{ page }} -->
                        <button @click="onApprove">Approve</button>
                    </div>
                </div>
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
</template>
