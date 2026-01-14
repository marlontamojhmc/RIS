<script setup lang="ts">

import VendorAppSidebarLayout from '@/layouts/vendor/VendorAppSidebarLayout.vue'

interface Accreditation {
  id: number
  form_number: string
  type?: string
  businessName?: string
  date?: string
  // add other fields as needed
}

// Props passed from Inertia controller
const props = defineProps<{
  accreditation: Accreditation
}>()

// Destructure for easier access
const { accreditation } = props
</script>
<template>
  <VendorAppSidebarLayout>
    
    <div class="max-w-4xl mx-auto p-6 bg-white shadow rounded-lg">
      <h1 class="text-2xl font-bold mb-4">Accreditation Details</h1>

      <div class="mb-2">
        <strong>Form Number:</strong>
        <span>{{ accreditation.form_number ?? '-' }}</span>
      </div>

      <div class="mb-2">
        <strong>Type:</strong>
        <span>{{ accreditation.type ?? '-' }}</span>
      </div>

      <div class="mb-2">
        <strong>Business Name:</strong>
        <span>{{ accreditation.business_name ?? '-' }}</span>
      </div>

      <div class="mb-2">
        <strong>Date:</strong>
        <span>{{ accreditation.date ?? '-' }}</span>
      </div>

      <div class="mt-4">
        <button
          @click="$inertia.visit('/loctr/applications/pending')"
          class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
        >
          Back to List
        </button>
      </div>
    </div>
  </VendorAppSidebarLayout>
</template>



<style scoped>
/* optional Tailwind overrides or extra styling */
</style>
