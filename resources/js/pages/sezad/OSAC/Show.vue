<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import OsacViewer from './OsacComponents/OsacViewer.vue';
import Button from '@/components/ui/button/Button.vue';
import OsacAppSidebarLayout from '@/layouts/Osac/OsacAppSidebarLayout.vue';
const page = usePage();
const props = defineProps({
  application: {
    type: Object, // changed to Object for single application
    required: true,
  },
  approver_status: {
    type: String,
    required: true,
  },
  group:{
    type:Object,
    required:true,
  }
});

// Modal state
const showModal = ref(false);
const comment = ref('');
const actionType = ref(null); // "reject" or "return"

// APPROVE
const handleApprove = () => {
  router.post(
    `/application-for-approval/${props.application.form_number}/approvers/${page.props.auth.user.id}/approve`,
    {},
    {
      onSuccess: () => {
        console.log('Application approved!');
      },
      onError: (errors) => {
        console.error('Approval failed', errors);
      },
    }
  );
};

// OPEN MODAL
const openModal = (type) => {
  actionType.value = type;
  showModal.value = true;
};

// CONFIRM RETURN / REJECT
const confirmAction = () => {
  
  router.post(
    `/application-for-approval/${props.application.form_number}/approvers/${page.props.auth.user.id}/${actionType.value}`,
    { comment: comment.value },
    {
      onSuccess: () => {
        console.log(`${actionType.value} successful`);
      },
      onError: (errors) => {
        console.error(`${actionType.value} failed`, errors);
      },
    }
  );

  // Reset modal
  showModal.value = false;
  comment.value = '';
};

// CANCEL
const cancelAction = () => {
  showModal.value = false;
  comment.value = '';
};
</script>

<template>
  <OsacAppSidebarLayout>
   
    <OsacViewer :application="props.application" :group="props.group" />
    
    <!-- Action bar -->
    <div
      v-if="props.approver_status === 'Pending'"
      class="flex justify-center gap-4 mt-8"
    >
      <Button
        variant="default"
        class="px-6 py-2 text-sm font-semibold"
        @click="handleApprove"
      >
        Approve
      </Button>

      <!-- <Button
        variant="destructive"
        class="px-6 py-2 text-sm font-semibold"
        @click="openModal('reject')"
      >
        Reject
      </Button> -->

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
        <h2 class="text-lg font-semibold mb-3">
          {{ actionType === 'reject' ? 'Reject Application' : 'Return Application' }}
        </h2>

        <label class="text-sm font-medium">Comment</label>
        <textarea
          v-model="comment"
          class="w-full mt-2 border border-gray-300 rounded-lg p-3 text-sm focus:ring focus:ring-primary"
          rows="4"
          placeholder="Enter your comment..."
        ></textarea>

        <div class="mt-4 flex justify-end gap-3">
          <Button variant="secondary" @click="cancelAction">
            Cancel
          </Button>

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
  </OsacAppSidebarLayout>
</template>
