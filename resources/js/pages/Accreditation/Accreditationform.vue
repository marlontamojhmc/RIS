<script setup lang="ts">
import { reactive, ref, onMounted, watch } from 'vue'
import { usePage } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'
import VendorAppSidebarLayout from '@/layouts/vendor/VendorAppSidebarLayout.vue'

// ----------------------
// Props
// ----------------------
const props = defineProps<{
  form_number: string
  application_id: string
  application_group_id: string
}>()

// ----------------------
// Reactive Form Model
// ----------------------
interface Form {
  date: string
  type: 'new' | 'renewal'
  businessName: string
  frequency: string
  services: number[]
  supplies: number[]
  address: string
  email: string
  contact: string
  representative: string
  privacyConsent: boolean
  form_number: string
  application_id: string
  application_group_id: string
}

const form = reactive<Form>({
  date: '',
  type: 'new',
  businessName: '',
  frequency: '',
  services: [],
  supplies: [],
  address: '',
  email: '',
  contact: '',
  representative: '',
  privacyConsent: false,
  form_number: props.form_number,
  application_id: props.application_id,
  application_group_id: props.application_group_id,
})

// ----------------------
// Dynamic Options
// ----------------------
interface Option {
  id: number
  name: string
}

const services = ref<Option[]>([])
const supplies = ref<Option[]>([])
const frequencies = ref<Option[]>([])

// ----------------------
// Load Options from API
// ----------------------
onMounted(async () => {
  try {
    const { data } = await axios.get('/api/accreditation/options')
    services.value = data.services
    supplies.value = data.supplies
    frequencies.value = data.frequencies
  } catch (error) {
    console.error('Failed to load form options', error)
  }
})

// ----------------------
// Submit Form
// ----------------------
const submit = () => {
  Inertia.post('/accreditation/store', form, {
    onSuccess: () => {
      alert('Accreditation submitted successfully!')
      resetForm()
    },
    onError: (errors) => {
      console.error(errors)
      alert('Please check your inputs.')
    },
  })
}

// ----------------------
// Watch for Prop Changes
// ----------------------
watch(
  () => props.form_number,
  (newVal) => {
    form.form_number = newVal
  }
)

watch(
  () => props.application_id,
  (newVal) => {
    form.application_id = newVal
  }
)

watch(
  () => props.application_group_id,
  (newVal) => {
    form.application_group_id = newVal
  }
)

// ----------------------
// Reset Form
// ----------------------
const resetForm = () => {
  Object.keys(form).forEach((key) => {
    // @ts-ignore
    form[key] = Array.isArray(form[key])
      ? []
      : key === 'privacyConsent'
      ? false
      : ''
  })
}
</script>



<template>
  <VendorAppSidebarLayout>
    <div class="bg-slate-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        
        <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-slate-200">
          <form class="p-8 space-y-10" @submit.prevent="submit">
            <h1>Vendor Accreditation</h1>
            <!-- Data Privacy -->
            <section class="bg-indigo-50/50 p-6 rounded-xl border border-indigo-100">
              <h2 class="text-indigo-900 font-bold flex items-center gap-2 mb-3">
                DATA PRIVACY CONSENT
              </h2>
              <p class="text-sm text-slate-600 italic">
                By signing this form, I authorize the organization to collect and process my data
                in accordance with the Data Privacy Act.
              </p>
              <label class="inline-flex items-center mt-4">
                <input type="checkbox" v-model="form.privacyConsent" required
                  class="w-4 h-4 rounded border-slate-300 text-indigo-600" />
                <span class="ml-3 text-sm font-semibold text-slate-700">
                  I Agree to the Data Privacy Consent
                </span>
              </label>
            </section>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="text-xs font-bold text-slate-500 uppercase">Date of Application</label>
                <input type="date" v-model="form.date" class="input" />
              </div>
              <div>
                <label class="text-xs font-bold text-slate-500 uppercase">Application Type</label>
                <select v-model="form.type" class="input">
                  <option value="new">New Application</option>
                  <option value="renewal">Renewal</option>
                </select>
              </div>
              <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-500 uppercase">
                  Registered Name of Service Provider
                </label>
                <input type="text" v-model="form.businessName" placeholder="Complete Business Name"
                  class="input" />
              </div>
              <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-500 uppercase">
                  Frequency of Services
                </label>
                <div class="flex flex-wrap gap-4 mt-2">
                  <label v-for="f in frequencies" :key="f.id" class="inline-flex items-center">
                    <input type="radio" :value="f.name" v-model="form.frequency" class="text-indigo-600" />
                    <span class="ml-2 text-sm text-slate-700">{{ f.name }}</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- Services & Supplies -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t pt-8">
              <div>
                <h3 class="font-bold border-b pb-2">SERVICES PROVIDED</h3>
                <label v-for="s in services" :key="s.id" class="flex items-center gap-3 text-sm mt-2">
                  <input type="checkbox" :value="s.id" v-model="form.services" />
                  {{ s.name }}
                </label>
              </div>
              <div>
                <h3 class="font-bold border-b pb-2">SUPPLIES PROVIDED</h3>
                <label v-for="s in supplies" :key="s.id" class="flex items-center gap-3 text-sm mt-2">
                  <input type="checkbox" :value="s.id" v-model="form.supplies" />
                  {{ s.name }}
                </label>
              </div>
            </div>

            <!-- Contact Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-8">
              <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-500 uppercase">Business Address</label>
                <textarea v-model="form.address" rows="2" class="input w-full"></textarea>
              </div>
              <div>
                <label class="text-xs font-bold text-slate-500 uppercase">Email</label>
                <input type="email" v-model="form.email" class="input" />
              </div>
              <div>
                <label class="text-xs font-bold text-slate-500 uppercase">Contact Number</label>
                <input type="text" v-model="form.contact" class="input" />
              </div>
              <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-500 uppercase">
                  Authorized Representative
                </label>
                <input type="text" v-model="form.representative" class="input" />
              </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4">
              <button type="submit"
                class="flex-1 bg-indigo-600 text-white py-4 rounded-xl font-bold">
                Submit Accreditation Request
              </button>
              <button type="reset" @click="reset" class="px-8 py-4 border rounded-xl">
                Clear Form
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </VendorAppSidebarLayout>
</template>

<style lang="postcss">
@reference "tailwindcss";

.input {
  @apply w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg
  focus:ring-2 focus:ring-indigo-500 focus:bg-white outline-none;
}
</style>
