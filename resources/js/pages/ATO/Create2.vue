<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/layouts/AppLayout.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'

// Props
const props = defineProps({
  application_id: { type: String, required: true },
  approver_group_id: { type: String, required: true },
  application_form_number: { type: String, required: true }
})

// Reactive form
const form = useForm({
  application_id: props.application_id,
  approver_group_id: props.approver_group_id,
  application_form_number: props.application_form_number,

  Enterprise: '',
  Sector: '',
  applicationType: '',
  leased_area_id: null,
  leased_area_label: '',
  price: '',

  businessStructure: '',
  natureOfContract: '',
  businessProfile: {
    businessName: '',
    parentCompany: '',
    taxpayerName: '',
    TIN: '',
  },
  pcic: {
    PCICPrimary: '',
    primaryLine: '',
    PCICSecondary: '',
    secondaryLine: '',
    emailPrimary: '',
    emailSecondary: '',
    location: '',
    officeAddress: '',
    contactPerson: '',
    contactNumber: '',
  },
  files: [
    { title: 'Letter of Intent', file: null },
    { title: 'Company Profile', file: null },
    { title: 'Valid Lease Contract', file: null },
    { title: 'OBO Clearance', file: null },
    { title: 'BIR Certificate of Registration', file: null },
  ]
})

// Options from API
const options = ref({
  Enterprise: [],
  Sector: [],
  Pricing: []
})
const loading = ref(false)

// Fetch options from API
const fetchOptions = async () => {

  loading.value = true
  try {
    const { data } = await axios.get('/api/ATO/options')
    options.value = data
  } catch (error) {
    console.error('Fetch error:', error.response?.data?.message || error.message)
  } finally {
    loading.value = false
  }
}

onMounted(fetchOptions)

// Compute leased-area options based on pricing & application type
const leasedAreas = computed(() => {
  if (!form.applicationType) return []
  return options.value.pricing
    .filter(p => p.application_type === form.applicationType)
    .map(p => ({
      id: p.id,
      min_area: p.min_area,
      max_area: p.max_area,
      label: p.max_area ? `${p.min_area}–${p.max_area} sqm` : `${p.min_area} sqm and above`,
      price: p.price
    }))
})
console.log(options.value.pricing);

// Watch leased area selection and update price automatically
watch(() => form.leased_area_id, (id) => {
  const selected = leasedAreas.value.find(l => l.id === id)
  if (selected) {
    form.price = selected.price
    form.leased_area_label = selected.label
  } else {
    form.price = ''
    form.leased_area_label = ''
  }
})

// Watch application type changes to reset leased area & price
watch(() => form.applicationType, () => {
  form.leased_area_id = null
  form.price = ''
  form.leased_area_label = ''
})

// File upload handlers
function handleFileChange(index, event) {
  form.files[index].file = event.target.files?.[0] ?? null
}

// Submit
function submitForm() {
  form.post('/ATO', {
    onStart: () => loading.value = true,
    onProgress: e => console.log('Upload progress:', e.percent),
    onFinish: () => loading.value = false,
    onSuccess: () => {
      form.reset()
      form.files.forEach(f => f.file = null)
    }
  })
}
</script>

<template>
  <AppLayout>
    <div class="p-6 space-y-8">
      <h3 class="text-xl font-bold mb-6 text-center">
        ATO (Authority to Operate) Application Form
      </h3>

      <div v-if="!loading" class="space-y-4">
        <!-- Enterprise -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <!-- Enterprise -->
  <div>
    <Label>Business Enterprise Classification</Label>
    <select v-model="form.Enterprise" class="border rounded p-2 w-full">
      <option disabled value="">Select Enterprise</option>
      <option
        v-for="item in options.Enterprise"
        :key="item.id"
        :value="item.id"
      >
        {{ item.name }}
      </option>
    </select>
  </div>

  <!-- Sector -->
  <div>
    <Label>Business Sector Classification</Label>
    <select v-model="form.Sector" class="border rounded p-2 w-full">
      <option disabled value="">Select Sector</option>
      <option
        v-for="sector in options.Sector"
        :key="sector.id"
        :value="sector.id"
      >
        {{ sector.name }}
      </option>
    </select>
  </div>
</div>

        <!-- Application Type -->
       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <!-- Type of Application -->
  <div>
    <Label>Type of Application</Label>
    <select v-model="form.applicationType" class="border rounded p-2 w-full">
      <option disabled value="">Select Type</option>
      <option value="New">New</option>
      <option value="Renewal">Renewal</option>
    </select>
  </div>

  <!-- Leased Area -->
  <div>
    <Label>Leased Area</Label>
    <select v-model="form.leased_area_id" class="border rounded p-2 w-full">
      <option disabled value="">Select Leased Area</option>
      <option v-for="area in leasedAreas" :key="area.id" :value="area.id">
        {{ area.label }}
      </option>
    </select>
  </div>
</div>

        <!-- Price (readonly) -->
        <div>
          <Label>Application Fee:</Label>
          <input type="text" class="border rounded p-2 w-full bg-gray-100" :value="`₱ ${form.price}`" readonly />
        </div>
      </div>

      <!-- Business Structure -->
      <div class="space-y-2">
        <Label>Business Structure</Label>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-1">
          <label class="flex items-center gap-2"><input type="radio" value="Sole Proprietor" v-model="form.businessStructure" />Sole Proprietor</label>
          <label class="flex items-center gap-2"><input type="radio" value="Partnership" v-model="form.businessStructure" />Partnership</label>
          <label class="flex items-center gap-2"><input type="radio" value="Corporation" v-model="form.businessStructure" />Corporation</label>
          <label class="flex items-center gap-2"><input type="radio" value="Cooperative" v-model="form.businessStructure" />Cooperative</label>
        </div>
      </div>

      <!-- File uploads -->
      <div class="space-y-4">
        <h2 class="text-lg font-bold">Upload Supporting Documents</h2>
        <div v-for="(row,index) in form.files" :key="index" class="bg-gray-50 p-4 rounded grid grid-cols-1 md:grid-cols-3 gap-3 items-center">
          <div class="p-2 border rounded bg-gray-100 font-semibold">{{ row.title }}</div>
          <Input type="file" class="w-full" @change="e => handleFileChange(index, e)" />
        </div>
      </div>

      <!-- Business Profile -->
      <div>
        <h2 class="text-lg font-bold mb-3">Business Enterprise Profile</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input v-model="form.businessProfile.businessName" placeholder="Trade's Name" />
          <Input v-model="form.businessProfile.parentCompany" placeholder="Name of Parent Company" />
          <Input v-model="form.businessProfile.taxpayerName" placeholder="Taxpayer's Name" />
          <Input v-model="form.businessProfile.TIN" placeholder="TIN" />

          <!-- Nature of Contract -->
          <div class="md:col-span-2">
            <Label>Nature of Contract</Label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
              <label class="flex items-center gap-2"><input type="radio" value="Direct Lease with BCDA/JHMC" v-model="form.natureOfContract" />Direct Lease with BCDA/JHMC</label>
              <label class="flex items-center gap-2"><input type="radio" value="Sub Leasee with Principal Locator" v-model="form.natureOfContract" />Sub Leasee with Principal Locator</label>
            </div>
          </div>
        </div>
      </div>

      <!-- PCIC -->
      <div>
        <h2 class="text-lg font-bold mb-3">PCIC Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input v-model="form.pcic.PCICPrimary" placeholder="PCIC Primary" />
          <Input v-model="form.pcic.primaryLine" placeholder="Line of Business" />
          <Input v-model="form.pcic.PCICSecondary" placeholder="PCIC Secondary" />
          <Input v-model="form.pcic.secondaryLine" placeholder="Secondary Line of Business" />
          <Input v-model="form.pcic.emailPrimary" type="email" placeholder="Primary Email Address" />
          <Input v-model="form.pcic.emailSecondary" type="email" placeholder="Secondary Email Address" />
          <Input v-model="form.pcic.location" placeholder="Location within JHMC" />
          <Input v-model="form.pcic.officeAddress" placeholder="Main Office Address" />
          <Input v-model="form.pcic.contactPerson" placeholder="Contact Person" />
          <Input v-model="form.pcic.contactNumber" placeholder="Contact Number" />
        </div>
      </div>

      <!-- Submit -->
      <div class="flex gap-4">
        <Button class="px-6 py-2 bg-green-600 text-white rounded" @click="submitForm">Submit</Button>
        <Button class="px-6 py-2 bg-red-600 text-white rounded" @click="form.reset()">Clear</Button>
      </div>

    </div>
  </AppLayout>
</template>
