<template>
  <form @submit.prevent="submitForm" class="p-6 max-w-6xl mx-auto bg-white border border-gray-200 shadow-sm space-y-6">
    
    <!-- Header -->
    <div class="flex justify-between items-start border-b pb-4">
      <div class="space-y-1">
        <input v-model="form.companyName" placeholder="John Hay" class="border-b border-gray-300 font-bold text-xl uppercase w-48" />
        <input v-model="form.title" placeholder="Gate Clearance" class="border-b border-gray-300 font-extrabold text-2xl uppercase w-64" />
      </div>
      <div class="text-right text-sm space-y-1">
        <input v-model="form.documentCode" placeholder="Document Code" class="border-b border-gray-300 w-32" />
        <input v-model="form.controlNo" placeholder="Control No." class="border-b border-gray-300 w-32" />
        <input v-model="form.gcNo" placeholder="GC No." class="border-b border-gray-300 w-32" />
      </div>
    </div>

    <!-- Clearance Granted To -->
    <div class="border-b pb-4 space-y-2">
      <p class="font-semibold">CLEARANCE is hereby granted to:</p>
      <input v-model="form.clearanceTo" placeholder="Company Name" class="border-b border-gray-300 font-bold text-lg w-full" />
      <p>to pass thru the <span class="font-semibold uppercase">GATE</span></p>
    </div>

    <!-- Detailed Description Table -->
    <div class="overflow-x-auto border-b pb-4">
      <div class="flex justify-between mb-2">
        <p class="font-semibold">Detailed Description of Articles</p>
        <button @click.prevent="showModal = true" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">
          + Add Article
        </button>
      </div>
      <table class="min-w-full border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="border px-2 py-1 text-left">MARKS BAG NUMBER</th>
            <th class="border px-2 py-1 text-left">QUANTITY</th>
            <th class="border px-2 py-1 text-left">DETAILED DESCRIPTION OF ARTICLES</th>
            <th class="border px-2 py-1 text-left">DECLARED VALUE</th>
            <th class="border px-2 py-1 text-center">ACTION</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(item, index) in form.table" :key="index" :class="item.saved ? 'bg-green-50' : ''">
            <td class="border px-2 py-1">{{ item.marksBag }}</td>
            <td class="border px-2 py-1">{{ item.quantity }}</td>
            <td class="border px-2 py-1">{{ item.description }}</td>
            <td class="border px-2 py-1">{{ item.declaredValue }}</td>
            <td class="border px-2 py-1 text-center space-x-1">
              <button @click.prevent="removeRow(index)" class="text-red-600 font-bold hover:text-red-800">X</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal for Adding Row -->
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white p-6 rounded shadow-lg w-96 relative">
        <h2 class="text-lg font-bold mb-4">Add Table Row</h2>
        <div class="space-y-3">
          <div>
            <label class="block text-sm font-semibold">Marks Bag Number</label>
            <input v-model="newRow.marksBag" class="border-b border-gray-300 w-full" />
          </div>
          <div>
            <label class="block text-sm font-semibold">Quantity</label>
            <input v-model="newRow.quantity" class="border-b border-gray-300 w-full" />
          </div>
          <div>
            <label class="block text-sm font-semibold">Description</label>
            <input v-model="newRow.description" class="border-b border-gray-300 w-full" />
          </div>
          <div>
            <label class="block text-sm font-semibold">Declared Value</label>
            <input type="number" v-model="newRow.declaredValue" class="border-b border-gray-300 w-full" />
          </div>
        </div>
        <div class="mt-4 flex justify-end space-x-2">
          <button @click.prevent="showModal = false" class="px-4 py-1 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
          <button @click.prevent="addRowFromModal" class="px-4 py-1 rounded bg-green-600 text-white hover:bg-green-700">Save</button>
        </div>
      </div>
    </div>

    <!-- Permit Validity Dropdown -->
    <div class="border-b pb-4 space-y-2">
      <p class="font-semibold">Select Validity Period:</p>
      <select v-model="form.selectedFeeId" class="border-b border-gray-300 w-full p-2">
        <option disabled value="">-- Select Validity --</option>
        <option v-for="fee in permitClearanceFees" :key="fee.id" :value="fee.id">
          {{ fee.title }} {{ fee.validity }} - ₱{{ fee.price }}
        </option>
      </select>
    </div>

    <!-- Fully Paid Under Section -->
    <div class="border-b pb-4 grid grid-cols-5 gap-4 text-sm">
      <div>
        <p class="font-semibold">SI NUMBER</p>
        <input v-model="form.siNumber" class="border-b border-gray-300 w-full" />
      </div>
      <div>
        <p class="font-semibold">DATE</p>
        <input type="date" v-model="form.date" class="border-b border-gray-300 w-full" />
      </div>
      <div>
        <p class="font-semibold">AMOUNT</p>
        <input type="text" v-model="form.amount" class="border-b border-gray-300 w-full" readonly />
      </div>
      <div>
        <p class="font-semibold">DATE OF DELIVERY</p>
        <input type="date" v-model="form.deliveryDate" class="border-b border-gray-300 w-full" />
      </div>
      <div>
        <p class="font-semibold">EXPIRATION DATE</p>
        <input type="date" v-model="form.expirationDate" class="border-b border-gray-300 w-full" />
      </div>
    </div>

    <!-- Authorizations -->
    <div class="border-b pb-4 space-y-2">
      <p class="font-semibold mb-2">AUTHORIZATIONS</p>
      <div v-for="auth in authorizations" :key="auth" class="flex items-center space-x-2">
        <input type="checkbox" :id="auth" :value="auth" v-model="form.authorizations" class="w-4 h-4" />
        <label :for="auth">{{ auth }}</label>
      </div>
    </div>

    <!-- Checklist of Requirements with File Upload -->
    <div class="border-b pb-4 space-y-2">
      <p class="font-semibold mb-2">CHECKLIST OF REQUIREMENTS</p>
      <div v-for="item in checklist" :key="item" class="flex items-center space-x-2 text-sm">
        <input type="checkbox" :id="item" :value="item" v-model="form.checklist" class="w-4 h-4" />
        <label :for="item">{{ item }}</label>
        <input 
          v-if="form.checklist.includes(item)" 
          type="file" 
          @change="handleFileUpload($event, item)" 
          class="border-b border-gray-300 w-64"
        />
      </div>
    </div>

    <!-- Submit Button -->
    <div class="text-right">
      <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Submit All</button>
    </div>
  </form>
</template>

<script setup>
import { reactive, ref, defineProps, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
  permitClearanceFees: { type: Array, required: true }
})

const showModal = ref(false)
const newRow = reactive({
  marksBag: '',
  quantity: '',
  description: '',
  declaredValue: null
})

const form = reactive({
  application_form_id: 550,
  companyName: '',
  title: '',
  documentCode: '',
  controlNo: '',
  gcNo: '',
  clearanceTo: '',
  table: [],
  selectedFeeId: null,
  amount: '',
  siNumber: '',
  date: '',
  deliveryDate: '',
  expirationDate: '',
  authorizations: [],
  checklist: [],
  checklistFiles: {}, // file objects keyed by checklist item
})

const authorizations = ['Authority to Operate','Accreditation','SEZ/OSAC Clearance','CDO Clearance']
const checklist = ['Commercial/Sales Invoice','Official Receipt/Delivery Receipt','Bill of Lading/Airway Bill','Pro-forma Invoice/Quotation','Affidavit/Declaration of Value','Others']

const addRowFromModal = async () => {
  form.table.push({
    marksBag: newRow.marksBag,
    quantity: newRow.quantity,
    description: newRow.description,
    declaredValue: newRow.declaredValue,
    saved: true
  })
  newRow.marksBag = ''
  newRow.quantity = ''
  newRow.description = ''
  newRow.declaredValue = null
  showModal.value = false
}

// Remove article row
const removeRow = (index) => {
  form.table.splice(index, 1)
}

// Handle file upload for checklist
const handleFileUpload = (event, key) => {
  form.checklistFiles[key] = event.target.files[0]
}

// Watch selected validity to update amount
watch(() => form.selectedFeeId, (newId) => {
  const fee = props.permitClearanceFees.find(f => f.id === newId)
  form.amount = fee ? `₱${Number(fee.price).toFixed(2)}` : ''
})

// Submit form using FormData
const submitForm = async () => {
  const formData = new FormData()
  formData.append('form', JSON.stringify(form))

  for (const key in form.checklistFiles) {
    if (form.checklistFiles[key]) {
      formData.append(`files[${key}]`, form.checklistFiles[key])
    }
  }

  try {
    const res = await axios.post('/permits/gatepass/submit', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    console.log('Form submitted', res.data)
  } catch (err) {
    console.error(err)
    alert('Submission failed')
  }
}
</script>
