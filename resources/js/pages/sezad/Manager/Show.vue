<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

import SezadManagerViewer from './ManagerComponents/SezadManagerViewer.vue';
import Button from '@/components/ui/button/Button.vue';
import SezadManagerAppSidebarLayout from '@/layouts/SezadManager/SezadManagerAppSidebarLayout.vue';

const page = usePage();

const props = defineProps({
  application: { type: Object, required: true },
  approver_status: { type: String, required: true },
  group: { type: Object, required: true },
  prev_approver:{type:String, required:true},
});

const showModal = ref(false);
const comment = ref('');
const actionType = ref(null);

// APPROVE ACTION
const handleApprove = () => {
  router.post(
    `/application-for-approval/${props.application.form_number}/approvers/${page.props.auth.user.id}/approve`,
    {},
    {
      onSuccess: () => console.log('Application approved'),
      onError: (errors) => console.error('Approval failed', errors),
    }
  );
};

// OPEN MODAL
const openModal = (type) => {
  actionType.value = type;
  showModal.value = true;
};

// ✔ ONLY LOGGING FOR NOW
const confirmAction = () => {
  console.log("Action:", actionType.value);
  console.log("Comment:", comment.value);

  showModal.value = false;
  comment.value = '';
};

const cancelAction = () => {
  showModal.value = false;
  comment.value = '';
};
</script>


<template>
  <SezadManagerAppSidebarLayout>
    
    <!-- Application Viewer -->
    <SezadManagerViewer 
      :application="props.application" 
      :group="props.group" 
    />

    <!-- Action Buttons -->
    <div 
       v-if="props.approver_status === 'Pending' && props.prev_approver === 'Approved'"
      class="flex justify-center gap-4 mt-8"
    >
      <Button
        variant="default"
        class="px-6 py-2 text-sm font-semibold"
        @click="handleApprove"
      >
        Approve
      </Button>

      <Button
        variant="destructive"
        class="px-6 py-2 text-sm font-semibold"
        @click="openModal('reject')"
      >
        Reject
      </Button>

      <Button
        class="px-6 py-2 text-sm font-semibold bg-green-600 text-white hover:bg-green-700"
        @click="openModal('return')"
      >
        Return
      </Button>
    </div>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
    >
      <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-xl">
        
        <!-- Title -->
        <h2 class="text-lg font-semibold mb-3">
          {{ actionType === 'reject' ? 'Reject Application' : 'Return Application' }}
        </h2>

        <!-- Comment -->
        <label class="text-sm font-medium">Comment</label>
        <textarea
          v-model="comment"
          class="w-full mt-2 border border-gray-300 rounded-lg p-3 text-sm focus:ring focus:ring-primary"
          rows="4"
          placeholder="Enter your comment..."
        ></textarea>

        <!-- Buttons -->
        <div class="mt-4 flex justify-end gap-3">
          <Button variant="secondary" @click="cancelAction">Cancel</Button>

          <Button
            :class="actionType === 'reject'
              ? 'bg-red-600 text-white hover:bg-red-700'
              : 'bg-green-600 text-white hover:bg-green-700'"
            @click="confirmAction"
          >
            {{ actionType === 'reject' ? 'Confirm Reject' : 'Confirm Return' }}
          </Button>
        </div>

      </div>
    </div>
  </SezadManagerAppSidebarLayout>
</template>
