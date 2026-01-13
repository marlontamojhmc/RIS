<script setup>
import { toRaw } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'
import AppLayout from '@/layouts/AppLayout.vue'

/**
 * Initialize Inertia form.
 * Keep structure simple and bind inputs directly to `form`.
 * Files must be actual File objects for Inertia to upload them.
 */
const props = defineProps({
    application_id: {
    type: String,
    required: true
  },approver_group_id:{
    type:String,
    required: true,
  },application_form_number:{
     type: String,
     required:true,
  }
})
const form = useForm({
  application_id: props.application_id,
  approver_group_id: props.approver_group_id,
  application_form_number:props.application_form_number,
  applicationType: '',            // "New" | "Renewal"
  businessStructure: '',
  natureOfContract: '',
  businessProfile: {
    businessName: '',
    parentCompany: '',
    taxpayerName: '',
    TIN: '',
  },
  pcic: {
    primaryLine: '',
    secondaryLine: '',
    emailPrimary: '',
    emailSecondary:'',
    location: '',
    officeAddress: '',
    contactPerson: '',
    contactNumber: '',
    PCICPrimary:'',
    PCICSecondary:'',
  },
  files: [
    { title: '', file: null }     // repeater rows
  ]
})

// Add a file row after given index
function addRow(index) {
  form.files.splice(index + 1, 0, { title: '', file: null })
}

// Remove a file row
function removeRow(index) {
  if (form.files.length > 1) form.files.splice(index, 1)
}

// Handle file change for a specific row
function handleFileChange(index, event) {
  const file = event.target.files?.[0] ?? null
  // assign directly to the reactive form object
  form.files[index].file = file
}

// Submit
function submitForm() {
  console.log(form.data())
  // Post with progress handlers if you want
  form.post('/ATO', {
    onStart: () => { /* optional */ },
    onProgress: (progressEvent) => {
      // progressEvent.percent is available
      // console.log('upload', progressEvent.percent)
    },
    onSuccess: () => {
      // example: reset form (keeps initial row)
      // form.reset('applicationType','selectedOption','natureOfContract','businessProfile','pcic')
      // form.files = [{ title:'', file:null }]
    },
  })
}
</script>

<template>
    <AppLayout>
  <div class="p-6 space-y-8">
    <h3 class="text-xl font-bold mb-6 text-center">
  ATO (Authority to Operate) Application Form
</h3>
    <!-- TYPE OF APPLICATION -->
    <div>
      <Label class="font-semibold">Type of Application</Label>
      <select v-model="form.applicationType" class="border rounded p-2 w-full mt-1">
        <option disabled value="">Select Type</option>
        <option value="New">New</option>
        <option value="Renewal">Renewal</option>
      </select>
    </div>

    <!-- BUSINESS STRUCTURE -->
    <div class="space-y-2">
      <Label class="font-semibold">Business Structure</Label>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mt-1">
        <label class="flex items-center gap-2">
          <input type="radio" value="Sole Proprietor" v-model="form.businessStructure" />
          Sole Proprietor
        </label>

        <label class="flex items-center gap-2">
          <input type="radio" value="Partnership" v-model="form.businessStructure" />
          Partnership
        </label>

        <label class="flex items-center gap-2">
          <input type="radio" value="Corporation" v-model="form.businessStructure" />
          Corporation
        </label>

        <label class="flex items-center gap-2">
          <input type="radio" value="Cooperative" v-model="form.businessStructure" />
          Cooperative
        </label>
      </div>
    </div>
     <!-- FILE UPLOAD REPEATER -->
    <div class="space-y-4">
      <h2 class="text-lg font-bold">Upload  Supporting Documents</h2>

      <div
        v-for="(row, index) in form.files"
        :key="index"
        class="bg-gray-50 p-4 rounded grid grid-cols-1 md:grid-cols-3 gap-3 items-center"
      >
        <!-- title -->
        <select v-model="row.title" class="border rounded p-2 w-full">
          <option disabled value="">Select File Title</option>
          <option>Letter of Intent</option>
          <option>Company Profile</option>
          <option>Valid Lease Contract</option>
          <option>OBO Clearance</option>
          <option>BIR Certificate of Registration</option>
        </select>

        <!-- file input — using starter Input component (works if it forwards attributes) -->
        <Input
          type="file"
          class="w-full"
          @change="e => handleFileChange(index, e)"
        />

        <!-- controls -->
        <div class="flex gap-2 items-center">
          <Button class="bg-blue-600 text-white" @click="addRow(index)">+</Button>
          <Button
            v-if="form.files.length > 1"
            class="bg-red-600 text-white"
            @click="removeRow(index)"
          >-</Button>
        </div>
      </div>
    </div>
    <!--end repeater-->
    <!-- BUSINESS PROFILE (2 columns) -->
    <div>
      <h2 class="text-lg font-bold mb-3">Business Enterprise Profile</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <Input v-model="form.businessProfile.businessName" placeholder="Trade's Name" />
        <Input v-model="form.businessProfile.parentCompany" placeholder="Name of Parent Company" />
        <Input v-model="form.businessProfile.taxpayerName" placeholder="Taxpayer's Name" />
        <Input v-model="form.businessProfile.TIN" placeholder="TIN" />

        <!-- Nature of Contract (full width) -->
        <div class="md:col-span-2">
          <Label class="font-semibold">Nature of Contract</Label>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-2">
            <label class="flex items-center gap-2">
              <input type="radio"
                     value="Direct Lease with BCDA/JHMC"
                     v-model="form.natureOfContract" />
              Direct Lease with BCDA/JHMC
            </label>

            <label class="flex items-center gap-2">
              <input type="radio"
                     value="Sub Leasee with Principal Locator"
                     v-model="form.natureOfContract" />
              Sub Leasee with Principal Locator
            </label>
          </div>
        </div>
      </div>
    </div>

    <!-- PCIC SECTION (2 columns) -->
    <div>
      <h2 class="text-lg font-bold mb-3">PCIC Information</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <Input v-model="form.pcic.PCICPrimary" placeholder="PCIC Primary" />
        <Input v-model="form.pcic.primaryLine" placeholder="Line of Business" />
        <Input v-model="form.pcic.PCICSecondary" placeholder="PCIC Secondary" />
        <Input v-model="form.pcic.secondaryLine" placeholder="Secondary Line of Business" />
        <Input v-model="form.pcic.emailPrimary" type="email" placeholder="Primary Email Address" />
        <Input v-model="form.pcic.emailSecondary" type="email" placeholder=" Secondary Email Address" />
        <Input v-model="form.pcic.location" placeholder="Location within JHMC" />
        <Input v-model="form.pcic.officeAddress" placeholder="Main Office Address" />
        <Input v-model="form.pcic.contactPerson" placeholder="Contact Person" />
        <Input v-model="form.pcic.contactNumber" placeholder="Contact Number" />
      </div>
    </div>

    

    <!-- SUBMIT -->
    <div>
      <Button class="px-6 py-2 bg-green-600 text-white rounded" @click="submitForm">
        Submit
      </Button>
      <Button class="px-6 py-2 bg-red-600 text-white rounded" @click="form.reset()">Clear</Button>
    </div>
  </div>
  </AppLayout>
</template>
