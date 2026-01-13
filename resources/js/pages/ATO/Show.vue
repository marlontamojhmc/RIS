<script setup>
import { Image } from "lucide-vue-next";
import LocatorAppSidebarLayout from "@/layouts/locator/LocatorAppSidebarLayout.vue";
import AppSidebarLayout from "@/layouts/app/AppSidebarLayout.vue";

const props = defineProps({
  ATOapplication: {
    type: Object,
    required: true,
  },
});

// Format date helper
const formatDate = (date) =>
  new Date(date).toLocaleDateString("en-US", {
    month: "long",
    day: "numeric",
    year: "numeric",
  });

// Computed values
const status = props.ATOapplication.application_form.status;
const displayStatus = status === "Approved" ? "Valid" : status;

const createdAt = new Date(props.ATOapplication.application_form.created_at);
const createdDate = formatDate(createdAt);
const validUntil =
  status === "Approved" ? `Valid until December 31, ${createdAt.getFullYear()}` : createdDate;
</script>

<template>
  <AppSidebarLayout :breadcrumbs="breadcrumbs">

    <!-- Header / Overview Card -->
    <div class="bg-white border rounded-xl shadow-sm p-6 mb-10">
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">

        <!-- Status -->
        <div class="bg-white border rounded-lg p-4 shadow-sm">
          <p class="text-sm text-muted-foreground"> ATO Status:</p>
          <p class="text-green-600 text-xl font-semibold">{{ displayStatus }}</p>
        </div>

        <!-- Created At / Valid Until -->
        <div class="bg-white border rounded-lg p-4 shadow-sm">
          <p class="text-sm text-muted-foreground">Valid Until: </p>
          <p class="text-green-600 text-xl font-semibold">{{ validUntil }}</p>
        </div>

      </div>
    </div>

    <!-- Main Details Section -->
    <div class="space-y-10 px-2 sm:px-6">
      <h2 class="text-2xl font-bold text-center tracking-tight">ATO Permit Details</h2>

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
          }"
          :key="label"
          class="bg-white border rounded-xl shadow-sm p-6 hover:shadow-md transition"
        >
          <p class="text-sm text-muted-foreground">{{ label }}</p>
          <p class="font-medium text-lg mt-1">{{ value }}</p>
        </div>

        <!-- Uploaded Files -->
        <div class="bg-white border rounded-xl shadow-sm p-6 md:col-span-2">
          <p class="text-sm text-muted-foreground mb-3">Uploaded Documents</p>

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

          <p v-else class="text-muted-foreground italic">No files uploaded</p>
        </div>

      </div>
    </div>

  </AppSidebarLayout>
</template>
