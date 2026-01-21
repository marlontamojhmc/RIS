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
 form_type:string
  date: string
  type: 'new' | 'renewal'
  businessName: string
  frequency: string
  services: number[]
  supplies: number[]
  classification:number[]
  address: string
  email: string
  contact: string
  representative: string
  privacyConsent: boolean
  files: File[]
  form_number: string
  application_id: string
  application_group_id: string
}

const form = reactive<AccreditationForm>({
  form_type:'ProvisionalGrant',
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
  files: [],
  classification:[],
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
const classification = ref<Option[]>([])

// Load dynamic options
onMounted(async () => {
  try {
    const { data } = await axios.get('/api/accreditation/options')
    services.value = data.services ?? []
    supplies.value = data.supplies ?? []
    frequencies.value = data.frequencies ?? []
    classification.value =data.classifications ?? []
    } catch (e) {
    console.error('Failed to load options', e)
  }
})

// Handle file selection
function handleFiles(event: Event) {
  const target = event.target as HTMLInputElement
  if (target.files) {
    form.files = Array.from(target.files)
  }
}

// Submit form using FormData
const submit = () => {
  const formData = new FormData()
  for (const key in form) {
    if (key === 'files') {
      form.files.forEach((file) => formData.append('files[]', file))
    } else {
      // @ts-ignore
      formData.append(key, form[key])
    }
  }

  router.post('/provisional/store', formData, {
    preserveScroll: true,
    headers: { 'Content-Type': 'multipart/form-data' },
    onSuccess: () => {
      alert('Accreditation submitted successfully')
      resetForm()
    },
    onError: (errors) => {
      console.error(errors)
      alert('Please check your inputs.')
    },
  })
}

// Reset form
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
  form.files = []
  form.c

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

            <!-- FORM TITLE -->
            <section class="p-6 rounded-xl bg-indigo-50 border border-indigo-100">
              <h1 class="text-center text-2xl font-bold mb-6">
                Provisional Grant Application Form
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
                <input type="checkbox" v-model="form.privacyConsent" required
                  class="w-4 h-4 text-indigo-600" />
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

              
            </div>

            <!-- SERVICES & SUPPLIES -->
            <div class="grid grid-cols-1 gap-8 border-t pt-8">
             
              <div>
                <h3 class="font-bold border-b pb-2 mb-2">Business Enterprise Clasiffication</h3>
                <label v-for="b in classification" :key="b.id" class="flex items-center gap-3 text-sm mt-2">
                  <input type="checkbox" :value="b.id" v-model="form.classification" />
                  {{ b.name }}
                </label>
              </div>
            </div>

            <!-- CONTACT INFO -->
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

            <!-- FILE UPLOAD -->
            <div class="mt-6">
              <label class="block w-full p-4 text-center border-2 border-dashed rounded-md cursor-pointer hover:border-indigo-500">
                <span class="text-gray-600">Click to select files</span>
                <input type="file" multiple class="hidden" @change="handleFiles" />
              </label>

              <ul class="mt-2 space-y-2">
                <li v-for="(file, index) in form.files" :key="index" class="text-sm text-gray-700">
                  {{ file.name }}
                </li>
              </ul>
            </div>

            <!-- ACTIONS -->
            <div class="flex gap-4 mt-6">
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
