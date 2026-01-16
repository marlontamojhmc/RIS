<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import VendorAppSidebarLayout from '@/layouts/vendor/VendorAppSidebarLayout.vue'

const props = defineProps<{
  form_number: string
  application_id: string
  application_group_id: string
}>()

interface AccreditationForm {
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

const form = reactive<AccreditationForm>({
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

interface Option {
  id: number
  name: string
}

const services = ref<Option[]>([])
const supplies = ref<Option[]>([])
const frequencies = ref<Option[]>([])

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/accreditation/options')
    services.value = data.services ?? []
    supplies.value = data.supplies ?? []
    frequencies.value = data.frequencies ?? []
  } catch (e) {
    console.error('Failed to load options', e)
  }
})

const submit = () => {
  router.post('/accreditation/store', form, {
    preserveScroll: true,
    onSuccess: () => {
      alert('Accreditation submitted successfully')
      resetForm()
    },
  })
}

const resetForm = () => {
  form.date = ''
  form.type = 'new'
  form.businessName = ''
  form.frequency = ''
  form.services = []
  form.supplies = []
  form.address = ''
  form.email = ''
  form.contact = ''
  form.representative = ''
  form.privacyConsent = false

  form.form_number = props.form_number
  form.application_id = props.application_id
  form.application_group_id = props.application_group_id
}
</script>

<template>
  <VendorAppSidebarLayout>
    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow-xl rounded-2xl border border-slate-200">
          <form class="p-8 space-y-10" @submit.prevent="submit">
<section class="p-6 rounded-xl bg-indigo-50 border border-indigo-100">
            <h1 class="text-center text-2xl font-bold mb-6">
  Supplier Accreditation Form
</h1>
</section>
            <!-- DATA PRIVACY -->
            <section class="p-6 rounded-xl bg-indigo-50 border border-indigo-100">
              <h2 class="text-indigo-900 font-bold mb-2">
                DATA PRIVACY CONSENT
              </h2>
              <p class="text-sm text-slate-600 italic">
                By signing this form, you authorize the collection and processing
                of personal data in accordance with the Data Privacy Act.
              </p>

              <label class="flex items-center gap-3 mt-4">
                <input
                  type="checkbox"
                  v-model="form.privacyConsent"
                  required
                  class="w-4 h-4 text-indigo-600"
                />
                <span class="text-sm font-semibold text-slate-700">
                  I agree to the Data Privacy Consent
                </span>
              </label>
            </section>

            <!-- BASIC INFO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                  Date of Application
                </label>
                <input type="date" v-model="form.date"
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg
                         focus:ring-2 focus:ring-indigo-500 outline-none" />
              </div>

              <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                  Application Type
                </label>
                <select v-model="form.type"
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg
                         focus:ring-2 focus:ring-indigo-500 outline-none">
                  <option value="new">New</option>
                  <option value="renewal">Renewal</option>
                </select>
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                  Registered Business Name
                </label>
                <input type="text" v-model="form.businessName"
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg
                         focus:ring-2 focus:ring-indigo-500 outline-none" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2">
                  Frequency of Services
                </label>
                <div class="flex flex-wrap gap-4">
                  <label v-for="f in frequencies" :key="f.id" class="flex items-center gap-2">
                    <input type="radio" :value="f.name" v-model="form.frequency" />
                    <span class="text-sm">{{ f.name }}</span>
                  </label>
                </div>
              </div>
            </div>

            <!-- SERVICES & SUPPLIES -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t pt-8">
              <div>
                <h3 class="font-bold border-b pb-2 mb-2">Services Provided</h3>
                <label v-for="s in services" :key="s.id" class="flex items-center gap-3 text-sm mt-2">
                  <input type="checkbox" :value="s.id" v-model="form.services" />
                  {{ s.name }}
                </label>
              </div>

              <div>
                <h3 class="font-bold border-b pb-2 mb-2">Supplies Provided</h3>
                <label v-for="s in supplies" :key="s.id" class="flex items-center gap-3 text-sm mt-2">
                  <input type="checkbox" :value="s.id" v-model="form.supplies" />
                  {{ s.name }}
                </label>
              </div>
            </div>

            <!-- CONTACT -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-8">
              <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                  Business Address
                </label>
                <textarea v-model="form.address" rows="2"
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg
                         focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
              </div>

              <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                  Email
                </label>
                <input type="email" v-model="form.email"
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg
                         focus:ring-2 focus:ring-indigo-500 outline-none" />
              </div>

              <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                  Contact Number
                </label>
                <input type="text" v-model="form.contact"
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg
                         focus:ring-2 focus:ring-indigo-500 outline-none" />
              </div>

              <div class="md:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">
                  Authorized Representative
                </label>
                <input type="text" v-model="form.representative"
                  class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg
                         focus:ring-2 focus:ring-indigo-500 outline-none" />
              </div>
            </div>

            <!-- ACTIONS -->
            <div class="flex gap-4">
              <button type="submit"
                class="flex-1 bg-indigo-600 text-white py-4 rounded-xl font-bold hover:bg-indigo-700">
                Submit Accreditation
              </button>

              <button type="button" @click="resetForm"
                class="px-8 py-4 border rounded-xl hover:bg-slate-50">
                Clear
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </VendorAppSidebarLayout>
</template>
