<script setup>
import { ref } from "vue";
import { Image } from "lucide-vue-next";
import AppLayout from "@/layouts/AppLayout.vue";
import TimeLine from "@/components/locator/TimeLine.vue";
import axios from "axios";

const props = defineProps({
  ATOapplication: {
    type: Object,
    required: true,
  },
});

/* ------------------------------
   Helpers
------------------------------ */
const formatDate = (date) =>
  new Date(date).toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  });

/* ------------------------------
   Computed Values
------------------------------ */
const status = props.ATOapplication.application_form.status;
const displayStatus = status === "Approved" ? "Valid" : status;

const createdAt = new Date(props.ATOapplication.application_form.created_at);
const createdDate = formatDate(createdAt);
const validUntil =
  status === "Approved"
    ? `Valid until December 31, ${createdAt.getFullYear()}`
    : createdDate;

/* ------------------------------
   Modal State & Approvers
------------------------------ */
const showModal = ref(false);
const approvers = ref([]);
const loadingApprovers = ref(false);
const errorApprovers = ref(null);

const openModal = async () => {
  showModal.value = true;
  await fetchApprovers();
};

const closeModal = () => {
  showModal.value = false;
};

const fetchApprovers = async () => {
  loadingApprovers.value = true;
  errorApprovers.value = null;

  try {
    const { data } = await axios.get(
      `/applications/${props.ATOapplication.application_form.id}/approvers`
    );
    approvers.value = data;
  } catch (err) {
    console.error(err);
    errorApprovers.value = "Failed to fetch approvers.";
  } finally {
    loadingApprovers.value = false;
  }
};
</script>

<template>
  <AppLayout>
    <!-- ================= HEADER / OVERVIEW ================= -->
    <div class="bg-white border rounded-xl shadow-sm p-6 mb-10">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
        <!-- Status -->
        <div class="bg-white border rounded-lg p-4 shadow-sm">
          <p class="text-sm text-muted-foreground">ATO Status</p>
          <p class="text-green-600 text-xl font-semibold">
            {{ displayStatus }}
          </p>
        </div>

        <!-- Valid Until -->
        <div class="bg-white border rounded-lg p-4 shadow-sm">
          <p class="text-sm text-muted-foreground">Valid Until</p>
          <p class="text-green-600 text-xl font-semibold">
            {{ validUntil }}
          </p>
        </div>
      </div>
    </div>

    <!-- ================= MAIN DETAILS ================= -->
    <div class="space-y-10 px-2 sm:px-6">
      <h2 class="text-2xl font-bold text-center tracking-tight">
        Authority to Operate (ATO) Permit Details
      </h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Field Cards -->
        <div
          v-for="(value, label) in {
            'Application Type': props.ATOapplication.application_type,
            'Business Structure': props.ATOapplication.business_structure,
            'Trade Name': props.ATOapplication.Trades_name,
            'Parent Company': props.ATOapplication.parent_company,
            'Taxpayer Name': props.ATOapplication.taxpayer_name,
            'TIN': props.ATOapplication.TIN,
            'Primary Line': props.ATOapplication.PrimaryLine,
            'Secondary Line': props.ATOapplication.SecondaryLine,
            'Nature of Contract': props.ATOapplication.nature_of_contract,
            'PCIC Primary Line': props.ATOapplication.pcic_primary_line,
            'PCIC Secondary Line': props.ATOapplication.pcic_secondary_line,
            'PCIC Primary Email': props.ATOapplication.pcic_Primary_email,
            'PCIC Secondary Email': props.ATOapplication.pcic_Secondary_email,
            'PCIC Location': props.ATOapplication.pcic_location,
            'PCIC Office Address': props.ATOapplication.pcic_office_address,
            'Contact Person': props.ATOapplication.pcic_contact_person,
            'Contact Number': props.ATOapplication.pcic_contact_number,
            'Application Date': formatDate(props.ATOapplication.application_date),
            'Fee': `₱${props.ATOapplication.price}`,
            'Form Number': props.ATOapplication.form_number,
          }"
          :key="label"
          class="bg-white border rounded-xl shadow-sm p-6 hover:shadow-md transition"
        >
          <p class="text-sm text-muted-foreground">{{ label }}</p>
          <p class="font-medium text-lg mt-1">{{ value }}</p>
        </div>

        <!-- ================= UPLOADED FILES ================= -->
        <div class="bg-white border rounded-xl shadow-sm p-6 md:col-span-2">
          <p class="text-sm text-muted-foreground mb-3">
            Attachments / Uploaded Supporting Documents
          </p>

          <div v-if="props.ATOapplication.uploads?.length">
            <ul class="space-y-3">
              <li
                v-for="(file, index) in props.ATOapplication.uploads"
                :key="index"
                class="flex items-center gap-2"
              >
                <Image class="w-4 h-4 text-blue-600" />
                <a
                  :href="file.file_url"
                  target="_blank"
                  class="text-blue-600 underline font-medium"
                >
                  {{ file.file_name }}
                </a>
              </li>
            </ul>
          </div>

          <p v-else class="text-muted-foreground italic">
            No files uploaded
          </p>
        </div>
      </div>
    </div>

    <!-- ================= ACTION BUTTON (END OF PAGE) ================= -->
    <div class="flex justify-end mt-12 px-6">
      <button
        @click="openModal"
        class="px-6 py-2 rounded-lg bg-blue-600 text-white font-medium
               hover:bg-blue-700 transition"
      >
        Track Process
      </button>
    </div>

    <!-- ================= MODAL ================= -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
      <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 relative">
        <!-- Close -->
        <button
          @click="closeModal"
          class="absolute top-3 right-3 text-gray-400 hover:text-gray-600"
        >
          ✕
        </button>

        <!-- Modal Header -->
        <h3 class="text-xl font-bold mb-4">ATO Approval Flow</h3>

        <!-- Modal Body -->
        <div class="max-h-[400px] overflow-y-auto">
          <div v-if="loadingApprovers" class="text-center py-10">
            Loading approvers...
          </div>

          <div v-else-if="errorApprovers" class="text-red-500 text-center py-10">
            {{ errorApprovers }}
          </div>

          <div v-else>
            <TimeLine :data="approvers" />
            <p v-if="!approvers.length" class="text-center text-muted-foreground italic py-4">
              No approvers assigned.
            </p>
          </div>
        </div>

        <!-- Modal Actions -->
        <div class="flex justify-end gap-3 mt-6">
          <button
            @click="closeModal"
            class="px-4 py-2 rounded-lg border hover:bg-gray-100 transition"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
