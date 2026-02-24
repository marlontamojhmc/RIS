<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import { router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

const props = defineProps({
    application: { type: Object, required: true },
    approver_status: { type: String, required: true },
    group: { type: Object, required: true },
    Prevapprover: { type: Object, required: true },
    price: { type: Number, required: true },
});

// Modal States
const showPaymentModal = ref(false);
const showReturnModal = ref(false);
const OR_Number = ref('');
const Remark = ref('');
const showApprove = ref(false);

// Compute price based on form_type
const price = computed(() => {
    if (props.application.form_type === 'Provisional') {
        return props.application.provisional_grant.price;
    } else if (props.application.form_type === 'Accreditation') {
        return props.application.accreditation.price;
    } else {
        return;
    }
    return 0;
});

// Open Modals
const openPaymentModal = () => (showPaymentModal.value = true);
const openReturnModal = () => (showReturnModal.value = true);

// Cancel / Close Modals
const cancelPayment = () => {
    showPaymentModal.value = false;
    OR_Number.value = '';
};
const cancelReturn = () => {
    showReturnModal.value = false;
    Remark.value = '';
};

// Confirm Actions
const confirmPayment = () => {
    if (!OR_Number.value.trim()) {
        alert('Please enter OR Number to proceed');
        return;
    }

    router.post(
        `/application-for-approval/${props.application.form_number}/approvers/${page.props.auth.user.id}/invoice`,
        {
            user: 'finance',
            OR_Number: OR_Number.value,
        },
        {
            onSuccess: () => (showApprove.value = true),
            onError: (errors) => console.error('Approval failed', errors),
        },
    );

    showPaymentModal.value = false;
    OR_Number.value = '';
};

const confirmReturn = () => {
    console.log('Return Remark:', Remark.value);
    showReturnModal.value = false;
    Remark.value = '';
};

// Approve action
const handleApprove = () => {
    router.post(
        `/application-for-approval/${props.application.form_number}/approvers/${page.props.auth.user.id}/approve`,
        { user: 'finance' },
        {
            onSuccess: () => console.log('Application approved!'),
            onError: (errors) => console.error('Approval failed', errors),
        },
    );
};
</script>

<template>
    <!-- <FinanceAppsidebarLayout>
  <FinanceViewer :application="props.application" :group="props.group" /> -->

    <!-- Display Price -->
    <p class="text-md mt-4 font-bold text-gray-800"></p>

    <!-- ACTION BAR -->
    <!-- <div v-if="props.approver_status === 'Pending' || props.Prevapprover.status === 'Approved'  " class="flex justify-center gap-4 mt-8"> -->
    <Button
        v-if="showApprove"
        variant="default"
        class="px-6 py-2 text-sm font-semibold"
        @click="handleApprove"
    >
        Approve
    </Button>

    <Button
        variant="destructive"
        class="px-6 py-2 text-sm font-semibold"
        @click="openPaymentModal"
    >
        Payment
    </Button>

    <Button
        class="bg-green-600 px-6 py-2 text-sm font-semibold text-white hover:bg-green-700"
        @click="openReturnModal"
    >
        Return
    </Button>

    <!-- PAYMENT MODAL -->
    <div
        v-if="showPaymentModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    >
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h2 class="mb-3 text-lg font-semibold">Permit Payment</h2>
            <label class="text-sm font-medium">Total Amount:</label>
            <p
                v-if="props.application.form_type === 'ATO'"
                class="text-md font-bold text-gray-800"
            >
                ₱{{ props.price }}
            </p>

            <p
                v-if="
                    props.application.form_type === 'Permit' &&
                    props.application.options?.length
                "
                class="text-md font-bold text-gray-800"
            >
                ₱{{ props.application.options[0]?.price }}
            </p>
            <label class="mt-3 text-sm font-medium">Enter OR Number:</label>
            <Input
                v-model="OR_Number"
                type="text"
                class="mt-2 w-full rounded-lg border border-gray-300 p-3 text-sm focus:ring focus:ring-primary"
                placeholder="Enter OR Number..."
            />
            <div class="mt-4 flex justify-end gap-3">
                <Button variant="secondary" @click="cancelPayment"
                    >Cancel</Button
                >
                <Button
                    class="bg-red-600 text-white hover:bg-red-700"
                    @click="confirmPayment"
                    >Confirm Payment</Button
                >
            </div>
        </div>
    </div>

    <!-- RETURN MODAL -->
    <div
        v-if="showReturnModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
    >
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl">
            <h2 class="mb-3 text-lg font-semibold">Return Application</h2>
            <label class="text-sm font-medium">Remark:</label>
            <textarea
                v-model="Remark"
                class="mt-2 w-full rounded-lg border border-gray-300 p-3 text-sm focus:ring focus:ring-primary"
                placeholder="Enter Remark"
            />
            <div class="mt-4 flex justify-end gap-3">
                <Button variant="secondary" @click="cancelReturn"
                    >Cancel</Button
                >
                <Button
                    class="bg-green-600 text-white hover:bg-green-700"
                    @click="confirmReturn"
                    >Confirm Return</Button
                >
            </div>
        </div>
    </div>
    <!-- </FinanceAppsidebarLayout> -->
</template>
