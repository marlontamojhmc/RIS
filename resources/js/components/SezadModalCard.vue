<script setup lang="ts">
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, watch } from 'vue';
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
    appUrl: string;
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
    uploads: [] as any[],
    // fee_option: [] as any[],
});
const currentIndex = ref(0);

const next = () => {
    currentIndex.value = (currentIndex.value + 1) % form.uploads.length;
};

const prev = () => {
    currentIndex.value =
        (currentIndex.value - 1 + form.uploads.length) % form.uploads.length;
};
// const page = usePage();
const toast = useToast();
const openPayment = ref(false);

const formPayment = useForm({
    is_number: '',
    amount: '',
});

const openPaymentFooter = (value: boolean) => {
    openPayment.value = value;
};
const userSequence =
    props.applicationProps?.application?.form_type == 'Permit'
        ? parseFloat(props.sequence[0]?.sequence)
        : parseFloat(props.sequence[1]?.sequence);
console.log('Sequence', userSequence);

const activeTab = ref(1);
// console.log('formTYpe',);
/* -----------------------
Actions
----------------------- */
const setTab = (tabNumber: number) => {
    activeTab.value = tabNumber;
};

const submitPayment = async () => {
    try {
        const response = await axios.post('/sezad/accept-payment', {
            application_forms_id: form.application_id,
            form_type: form.form_type,
            form_number: form.form_number,
            form_id: form.form_id,
            is_number: formPayment.is_number,
            amount: formPayment.amount,
        });

        if (response.data.success) {
            toast.success('Payment processed successfully.');
            openPayment.value = false;

            // Update local status immediately
            const financeApprover = form.approvers.find(
                (a) => a.role === 'Finance',
            );
            if (financeApprover) financeApprover.status = 'Approved';

            form.status = response.data.status || 'Approved';
        }
    } catch (error) {
        console.error(error);
        toast.error('Payment failed.');
    }
};

const onApprove = async () => {
    const lastApprover = form.approvers[form.approvers.length - 1];
    const isLastApprover =
        (lastApprover.approver?.id || lastApprover.approver_id) ===
        props.userId;

    try {
        const response = await axios.post('/sezad/approve', {
            user_id: props.userId,
            application_form_id: form.application_id,
            approver_group_id: props.userRole.approver_group_id,
            sequence: Number(props.userRole.sequence),
            isLastApprover: isLastApprover,
        });

        if (response.data.success) {
            // Find current user's entry in the approvers list and update it
            const currentApprover = form.approvers.find(
                (a) => a.role === props.userRole.role,
            );
            if (currentApprover) {
                currentApprover.status = 'Approved';
            }

            form.status = response.data.status;
            toast.success('Application approved successfully.');
        }
    } catch (error) {
        console.error(error);
        toast.error('Approval failed.');
    }
};

/* -----------------------
Helpers
----------------------- */
const formatDate = (value: string | null) => {
    if (!value) return '';
    return new Intl.DateTimeFormat('en-US', {
        year: 'numeric',
        month: 'long',
        day: '2-digit',
    }).format(new Date(value));
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
        form.uploads = (source as any)?.uploads ?? [];
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
console.log('FORM', form);
// console.log('ID', props.userId);
console.log('apps', props.applicationProps);
console.log('axios', props.appUrl);
</script>

<template>
    <Card class="relative z-10 max-h-[90vh] w-full border-2 border-amber-50">
        <CardHeader>
            <div class="flex flex-row justify-between">
                <div class="flex flex-col">
                    <CardTitle>{{ form.form_title }}</CardTitle>

                    <span class="">
                        <strong>Status: </strong>{{ form.status }}</span
                    >
                    <span>
                        <strong>Locator: </strong>
                        {{ form.locator_name }}
                    </span>
                </div>
                <div class="mr-5 space-y-1 text-sm">
                    <p>
                        <strong>App Date:</strong>
                        {{ formatDate(form.application_date) }}
                    </p>
                    <p>
                        <strong>Approved Date:</strong>
                        {{
                            form.status == 'Approved'
                                ? formatDate(form.approved_date)
                                : '---'
                        }}
                    </p>
                    <p>
                        <strong>Control #:</strong>
                        {{ form.control_number }}
                    </p>
                </div>
            </div>
            <div class="w-full border-b border-gray-200">
                <ol
                    class="hide-scrollbar flex overflow-x-auto whitespace-nowrap"
                >
                    <li
                        @click="setTab(1)"
                        :class="[
                            'mr-4',
                            'cursor-pointer',
                            'rounded-md',
                            'px-4',
                            'py-2',
                            'text-xs',
                            'font-medium',
                            'hover:bg-gray-100',
                        ]"
                    >
                        Application Information
                    </li>
                    <li
                        @click="setTab(2)"
                        :class="[
                            'mr-4',
                            'cursor-pointer',
                            'rounded-md',
                            'px-4',
                            'py-2',
                            'text-xs',
                            'font-medium',
                            'hover:bg-gray-100',
                        ]"
                    >
                        Approvers
                    </li>
                    <!-- border-b-2 border-primary -->
                    <li
                        @click="setTab(3)"
                        :class="[
                            'mr-4',
                            'cursor-pointer',
                            'rounded-md',
                            'px-4',
                            'py-2',
                            'text-xs',
                            'font-medium',
                            'hover:bg-gray-100',
                        ]"
                    >
                        Uploads
                    </li>
                </ol>
            </div>
        </CardHeader>

        <CardContent class="space-y-4">
            <!-- {{ activeTab }} -->
            <div class="flex w-full flex-col items-center justify-between">
                <!-- information -->
                <div v-if="activeTab == 1" class="w-full p-4">
                    <span> Form ID: {{ form.form_id }} </span><br />
                    <span> Form Number: {{ form.form_number }} </span><br />
                    <span> Form Type: {{ form.form_type }} </span><br />
                    <hr />
                    <span>
                        Amount:
                        {{ (form.user_app_selection as any).amount }} </span
                    ><br />
                    <span>
                        Date From:
                        {{
                            (form.user_app_selection as any).selected_at
                        }} </span
                    ><br />
                    <span>
                        Date To:{{
                            (form.user_app_selection as any).Expired_at
                        }} </span
                    ><br />
                    <span v-if="(form.user_app_selection as any)?.fee_option">
                        Code:
                        {{ (form.user_app_selection as any).fee_option.code }}
                    </span>
                    <span v-else class="text-gray-400 italic">
                        No fee option selected </span
                    ><br />
                    <span v-if="(form.user_app_selection as any)?.fee_option">
                        Description:
                        {{
                            (form.user_app_selection as any).fee_option
                                .description
                        }}
                    </span>
                    <span v-else class="text-gray-400 italic">
                        No fee description
                    </span>
                    <br />
                    <span
                        >Price:
                        {{
                            form.user_app_selection &&
                            (form.user_app_selection as any)?.fee_option
                                ? (form.user_app_selection as any).fee_option
                                      .price
                                : 'N/A'
                        }}
                    </span>
                    <br />
                    <span
                        >Fee Title:
                        {{
                            form.user_app_selection &&
                            (form.user_app_selection as any)?.fee_option
                                ? (form.user_app_selection as any).fee_option
                                      .title
                                : 'N/A'
                        }} </span
                    ><br />
                    <span>
                        Validity:
                        {{
                            form.user_app_selection &&
                            (form.user_app_selection as any)?.fee_option
                                ? (form.user_app_selection as any).fee_option
                                      .validity
                                : 'N/A'
                        }} </span
                    ><br />
                    <span
                        >Value:
                        {{
                            form.user_app_selection &&
                            (form.user_app_selection as any)?.fee_option
                                ? (form.user_app_selection as any).fee_option
                                      .value
                                : 'N/A'
                        }}
                    </span>
                </div>
                <!-- approvers status -->
                <div v-else-if="activeTab == 2" class="w-full p-4">
                    <div v-if="form.approvers.length">
                        <ol class="space-y-1">
                            <li
                                v-for="(a, i) in form.approvers"
                                :key="i"
                                class="flex items-center justify-between rounded-lg border bg-gray-50 px-2 py-1 shadow-sm"
                            >
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs font-bold text-gray-900"
                                        >{{
                                            a.approver?.name || 'Pending...'
                                        }}</span
                                    >
                                    <span
                                        class="text-[10px] text-gray-500 uppercase"
                                        >{{ a.role }}</span
                                    >
                                </div>
                                <span
                                    class="rounded-full border px-2 py-0.5 text-[10px] font-bold capitalize"
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
                <!-- Uploads-->
                <div v-else-if="activeTab == 3" class="w-full p-4">
                    <div
                        v-if="form.uploads.length > 0"
                        class="relative mx-auto w-full max-w-2xl"
                    >
                        <div
                            class="flex h-64 items-center justify-center overflow-hidden rounded-lg border border-gray-200 bg-gray-50"
                        >
                            <a
                                :href="`${appUrl}/storage/${form.uploads[currentIndex].file_path}`"
                                target="_blank"
                                title="Click to view full size"
                                class="flex h-full w-full cursor-zoom-in items-center justify-center"
                            >
                                <img
                                    :src="`${appUrl}/storage/${form.uploads[currentIndex].file_path}`"
                                    alt="Uploaded Image"
                                    class="h-full w-full object-contain transition-opacity duration-300 hover:opacity-90"
                                />

                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-black/5 opacity-0 transition-opacity group-hover:opacity-100"
                                >
                                    <span
                                        class="rounded-full bg-white/90 px-3 py-1 text-xs font-medium shadow-sm"
                                    >
                                        View Full Size ↗
                                    </span>
                                </div>
                            </a>
                        </div>

                        <div v-if="form.uploads.length > 1">
                            <button
                                @click="prev"
                                class="absolute top-1/2 left-2 -translate-y-1/2 rounded-full bg-white/80 p-2 shadow hover:bg-white"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                            </button>
                            <button
                                @click="next"
                                class="absolute top-1/2 right-2 -translate-y-1/2 rounded-full bg-white/80 p-2 shadow hover:bg-white"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-6 w-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </button>
                        </div>

                        <div class="mt-2 text-center text-xs text-gray-500">
                            {{ currentIndex + 1 }} of
                            {{ form.uploads.length }} —
                            {{ form.uploads[currentIndex].file_name }}
                        </div>
                    </div>

                    <div
                        v-else
                        class="py-10 text-center text-sm text-gray-400 italic"
                    >
                        No uploads found.
                    </div>
                </div>
            </div>
            <!-- Approvers -->
            <div
                class="border-t pt-4"
                v-if="normalizeStatus(form.status) !== 'approved'"
            >
                <div v-for="(item, index) in form.approvers" :key="index">
                    <div
                        v-if="
                            item.role === userRole.role &&
                            normalizeStatus(item.status) === 'pending'
                        "
                    >
                        <div v-if="userRole.role === 'Finance'">
                            <div class="flex gap-2">
                                <button
                                    v-if="!openPayment"
                                    @click="openPaymentFooter(true)"
                                    class="rounded-md bg-blue-600 px-4 py-2 text-sm text-white transition hover:bg-blue-700"
                                >
                                    Proceed Payment
                                </button>
                                <button
                                    v-else
                                    @click="openPaymentFooter(false)"
                                    class="rounded-md bg-red-600 px-4 py-2 text-sm text-white transition hover:bg-red-700"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>

                        <div v-else>
                            <button
                                v-show="
                                    form.approvers[userSequence - 1].status ==
                                        'Approved' &&
                                    form.approvers[userSequence].status ==
                                        'Pending'
                                "
                                @click="onApprove"
                                class="rounded-md bg-green-600 px-6 py-2 font-semibold text-white transition hover:bg-green-700"
                            >
                                Approve Application
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>

        <CardFooter
            v-if="openPayment"
            class="flex flex-col gap-4 border-t bg-gray-50 p-4"
        >
            <div class="grid w-full grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-600"
                        >IS Number</label
                    >
                    <Input
                        type="number"
                        v-model="formPayment.is_number"
                        placeholder="Enter IS Number"
                    />
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-bold text-gray-600"
                        >Amount</label
                    >
                    <Input
                        type="number"
                        v-model="formPayment.amount"
                        placeholder="0.00"
                    />
                </div>
            </div>
            <button
                class="w-full rounded-md bg-primary py-2 font-bold text-white transition hover:bg-primary/90"
                @click="submitPayment"
            >
                Confirm & Accept Payment
            </button>
        </CardFooter>
    </Card>
</template>
