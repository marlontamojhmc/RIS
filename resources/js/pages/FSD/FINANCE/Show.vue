<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import FinanceViewer from './FinanceComponents/FinanceViewer.vue';
import Button from '@/components/ui/button/Button.vue';
import FinanceAppsidebarLayout from '@/layouts/Finance/FinanceAppsidebarLayout.vue';
import Input from '@/components/ui/input/Input.vue';

const page = usePage();

const props = defineProps({
  application: { type: Array, required: true },
  approver_status: { type: String, required: true },
  group: { type: Object, required: true }
});

// Modal States
const showPaymentModal = ref(false);
const showReturnModal = ref(false);
const OR_Number = ref('');
const Remark = ref('');
const showApprove = ref(false);

// Open Modals
const openPaymentModal = () => showPaymentModal.value = true;
const openReturnModal = () => showReturnModal.value = true;

// Confirm Actions
const confirmPayment = () => {
  router.post(
    `/application-for-approval/${props.application.form_number}/approvers/${page.props.auth.user.id}/invoice`,
    { user: 'finance',
      IS: OR_Number.value,
     },
    {
      onSuccess: () =>  showApprove.value = true,
      onError: (errors) => console.error('Approval failed', errors),
    }
  );
  //   console.log('OR Number:', OR_Number.value)
   
  // if (!OR_Number.value.trim()) {
  //   alert('Please enter OR Number to proceed');
  //   return;
  //}
  
  showPaymentModal.value = false;
  OR_Number.value = '';
};

const confirmReturn = () => {
  console.log('Return Remark:', Remark.value);

  showReturnModal.value = false;
  Remark.value = '';
};

// Cancel / Close Modals
const cancelPayment = () => { showPaymentModal.value = false; OR_Number.value = ''; };
const cancelReturn = () => { showReturnModal.value = false; Remark.value = ''; };

// Approve action
const handleApprove = () => {
  router.post(
    `/application-for-approval/${props.application.form_number}/approvers/${page.props.auth.user.id}/approve`,
    { user: 'finance' },
    {
      onSuccess: () => console.log('Application approved!'),
      onError: (errors) => console.error('Approval failed', errors),
    }
  );
};
</script>

<template>
<FinanceAppsidebarLayout>
  <FinanceViewer :application="props.application" :group="props.group" />
  <!-- ACTION BAR -->
  <div v-if="props.approver_status === 'Pending'" class="flex justify-center gap-4 mt-8">
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
      class="px-6 py-2 text-sm font-semibold bg-green-600 text-white hover:bg-green-700"
      @click="openReturnModal"
    >
      Return
    </Button>
  </div>

  <!-- PAYMENT MODAL -->
  <div v-if="showPaymentModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-xl">
      <h2 class="text-lg font-semibold mb-3">Permit Payment</h2>
      <label class="text-sm font-medium">Total Amount:</label>
      <p class="text-md text-gray-800 font-bold">₱{{ props.application.selections[0].amount }}</p>
      <label class="text-sm font-medium mt-3">Enter OR Number:</label>
      <Input
        v-model="OR_Number"
        type="text"
        class="w-full mt-2 border border-gray-300 rounded-lg p-3 text-sm focus:ring focus:ring-primary"
        placeholder="Enter OR Number..."
      />
      <div class="mt-4 flex justify-end gap-3">
        <Button variant="secondary" @click="cancelPayment">Cancel</Button>
        <Button class="bg-red-600 text-white hover:bg-red-700" @click="confirmPayment">Confirm Payment</Button>
      </div>
    </div>
  </div>

  <!-- RETURN MODAL -->
  <div v-if="showReturnModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-xl">
      <h2 class="text-lg font-semibold mb-3">Return Application</h2>
      <label class="text-sm font-medium">Remark:</label>
      <textarea
        v-model="Remark"
        class="w-full mt-2 border border-gray-300 rounded-lg p-3 text-sm focus:ring focus:ring-primary"
        placeholder="Enter Remark"
      />
      <div class="mt-4 flex justify-end gap-3">
        <Button variant="secondary" @click="cancelReturn">Cancel</Button>
        <Button class="bg-green-600 text-white hover:bg-green-700" @click="confirmReturn">Confirm Return</Button>
      </div>
    </div>
  </div>
</FinanceAppsidebarLayout>
</template>
