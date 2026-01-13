<template>
  <div v-if="showModal">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-white/40 backdrop-blur-md flex items-center justify-center z-50">
      <div class="bg-white w-full max-w-4xl rounded-xl shadow-xl p-6 overflow-y-auto max-h-[90vh] relative">

        <!-- Close Button -->
        <button
          @click="closeModal"
          class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-xl font-bold"
        >
          &times;
        </button>

        <h2 class="text-xl font-semibold mb-4">Accreditation for Service Providers / Suppliers</h2>

        <!-- FORM GRID -->
        <div class="grid grid-cols-3 gap-4">

          <!-- Business Enterprise Name (Disabled) -->
          <div class="col-span-3">
            <label class="block text-sm font-medium mb-1">Business Enterprise Name</label>
            <input type="text" v-model="form.business_name" disabled class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg bg-gray-100" />
          </div>

          <!-- Parent Company -->
          <div>
            <label class="block text-sm font-medium mb-1">Parent Company (Optional)</label>
            <input type="text" v-model="form.parent_company" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>

          <!-- Contract Type -->
          <div class="col-span-3">
            <label class="block text-sm font-medium mb-2">Contract Type</label>
            <div class="flex items-center gap-6">
              <label class="flex items-center gap-2">
                <input type="radio" value="direct" v-model="form.contract_type" /> Direct Lease with BCDA/JHMC
              </label>
              <label class="flex items-center gap-2">
                <input type="radio" value="sublessee" v-model="form.contract_type" /> Sublessee with Principal Locator
              </label>
            </div>
            <div v-if="form.contract_type === 'sublessee'" class="mt-3">
              <label class="block text-sm font-medium mb-1">If Sublessee, Principal Locator Name</label>
              <input type="text" v-model="form.principal_locator" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
            </div>
          </div>

          <!-- Taxpayer Name & TIN -->
          <div>
            <label class="block text-sm font-medium mb-1">Taxpayer's Name</label>
            <input v-model="form.taxpayer_name" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">TIN</label>
            <input v-model="form.tin" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>

          <!-- PSIC & Line of Business -->
          <div>
            <label class="block text-sm font-medium mb-1">PSIC Primary Number</label>
            <input v-model="form.psic_primary" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Line of Business (Primary)</label>
            <input v-model="form.lob_primary" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">PSIC Secondary Number</label>
            <input v-model="form.psic_secondary" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Line of Business (Secondary)</label>
            <input v-model="form.lob_secondary" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>

          <!-- Trade Name & Email -->
          <div>
            <label class="block text-sm font-medium mb-1">Trade Name</label>
            <input v-model="form.trade_name" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Email Address</label>
            <input v-model="form.email" type="email" disabled class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg bg-gray-100" />
          </div>

          <!-- Location & Office Address -->
          <div>
            <label class="block text-sm font-medium mb-1">Location within JHSEZ</label>
            <input v-model="form.jhsez_location" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>
          <div class="col-span-3">
            <label class="block text-sm font-medium mb-1">Main Office Address</label>
            <input v-model="form.office_address" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
          </div>

          <!-- Contact Person & Contact Number Side by Side -->
          <div class="col-span-3 grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Contact Person</label>
              <input v-model="form.contact_person" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Contact Number</label>
              <input v-model="form.contact_number" type="text" class="w-full px-3 py-2 border-2 border-[#0F75BC] rounded-lg" />
            </div>
          </div>

        </div>

        <!-- ACTION BUTTONS -->
        <div class="flex justify-end mt-6 gap-4">
          <button
            @click="closeModal"
            class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100"
          >
            Close
          </button>
          <button
            @click="submitForm"
            class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700"
          >
            Submit
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const showModal = ref(true);

const form = ref({
  business_name: page.props.auth.user.name,
  parent_company: null,
  contract_type: null,
  principal_locator: "",
  taxpayer_name: "",
  tin: "",
  psic_primary: "",
  lob_primary: "",
  psic_secondary: "",
  lob_secondary: "",
  trade_name: "",
  email: page.props.auth.user.email,
  jhsez_location: "",
  office_address: "",
  contact_person: "",
  contact_number: "",
  documents: []
});

function closeModal() {
  showModal.value = false;
}

function submitForm() {
  console.log("Form submitted:", form.value);
  // You can replace this with Inertia post or API call
  // e.g. Inertia.post('/register-business', form.value)
}
</script>
