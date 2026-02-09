<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '@/layouts/AppLayout.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'
import { useToast } from 'vue-toastification'
import { usePage } from '@inertiajs/inertia-vue3'

const toast = useToast()
const page = usePage()

// Props
const props = defineProps({
  application_id: { type: String, required: true },
  approver_group_id: { type: Number, required: true },
  application_form_number: { type: String, required: true },
  application_group_id:{type: Number,required:true},
  form_id:{ type:Number, required:true},
  
})

// Form
const form = useForm({
  form_id: props.form_id,
  application_id: props.application_id,
  approver_group_id: props.approver_group_id,
  application_form_number: props.application_form_number,

  date_of_application: '',
  registered_business_name: '',

  applicationType: '',
  unit: '',
  price: '',

  location_of_units: '',
  business_owner_name: '',
  contact_numbers: '',
  official_email: '',

  authorized_representative_name: '',
  authorized_representative_contact: '',

  caretakers: [
    { name: '', role: '' }
  ]
})

// Options
const options = ref({ unit: [] })
const loading = ref(false)

const fetchOptions = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/ATO/options')
    options.value = data
  } finally {
    loading.value = false
  }
}

onMounted(fetchOptions)

// Units
const filteredUnits = computed(() => {
  if (!form.applicationType) return []
  return options.value.unit.filter(
    u => u.application_type === form.applicationType
  )
})

watch(() => form.unit, (unitId) => {
  const selectedUnit = options.value.unit.find(u => u.id === unitId)
  form.price = selectedUnit ? selectedUnit.price : ''
})

watch(() => form.applicationType, () => {
  form.unit = ''
  form.price = ''
})

// Caretaker handlers
function addCaretaker() {
  form.caretakers.push({ name: '', role: '' })
}

function removeCaretaker(index) {
  form.caretakers.splice(index, 1)
}

// Submit
function submitForm() {
  form.post('/ATO', {
    onStart: () => loading.value = true,
    onFinish: () => loading.value = false,
    onSuccess: (page) => {
      if (page.props.message) {
        toast.success(page.props.message) // ← pass the message here
      }
      form.reset() // resets the form
    },
    onError: (errors) => {
      toast.error('Submission failed')
    }
  })
}
</script>

<template>
  <AppLayout>
    <div class="p-6 space-y-8">
   
      <h3 class="text-xl font-bold text-center">
        Authority to Operate (ATO) Application Form
      </h3>

      <div v-if="!loading" class="space-y-6">

        <!-- Date + Business Name -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <Label>Date of Application</Label>
            <Input type="date" v-model="form.date_of_application" />
          </div>

          <div>
            <Label>Registered Business Name</Label>
            <Input v-model="form.registered_business_name" />
          </div>
        </div>

        <!-- Application Type + Units -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <Label>Type of Application</Label>
            <select v-model="form.applicationType" class="border rounded p-2 w-full">
              <option disabled value="">Select Type</option>
              <option value="New">New</option>
              <option value="Renewal">Renewal</option>
            </select>
          </div>

          <div>
            <Label>No. of Units</Label>
            <select
              v-model="form.unit"
              :disabled="!form.applicationType"
              class="border rounded p-2 w-full disabled:bg-gray-100"
            >
              <option disabled value="">Select Units</option>
              <option v-for="unit in filteredUnits" :key="unit.id" :value="unit.id">
                {{ unit.label }}
              </option>
            </select>
          </div>
        </div>

        <!-- Fee -->
        <div>
          <Label>Application Fee</Label>
          <Input :value="form.price ? `₱ ${form.price}` : ''" readonly />
        </div>

        <!-- Business Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input v-model="form.location_of_units" placeholder="Location of Accommodation Unit/s" />
          <Input v-model="form.business_owner_name" placeholder="Name of Business Owner" />
          <Input v-model="form.contact_numbers" placeholder="Contact Number/s" />
          <Input type="email" v-model="form.official_email" placeholder="Official Email Address" />
        </div>

        <!-- Authorized Representative -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Input v-model="form.authorized_representative_name" placeholder="Authorized Representative Name" />
          <Input v-model="form.authorized_representative_contact" placeholder="Authorized Representative Contact No." />
        </div>

        <!-- Caretaker Section -->
        <div class="space-y-4">
          <h4 class="font-bold">Caretaker(s)</h4>

          <div
            v-for="(caretaker, index) in form.caretakers"
            :key="index"
            class="grid grid-cols-1 md:grid-cols-2 gap-4"
          >
            <Input v-model="caretaker.name" placeholder="Caretaker Name" />
            <Input v-model="caretaker.role" placeholder="Role" />
          </div>

          <div class="flex gap-2">
            <Button @click="addCaretaker" type="button">Add Caretaker</Button>
            <Button
              v-if="form.caretakers.length > 1"
              @click="removeCaretaker(form.caretakers.length - 1)"
              type="button"
              class="bg-red-500 text-white"
            >
              Remove
            </Button>
          </div>
        </div>

        <!-- Submit -->
        <div class="flex gap-4">
          <Button class="bg-green-600 text-white" @click="submitForm">
            Submit
          </Button>
          <Button class="bg-red-600 text-white" @click="form.reset()">
            Clear
          </Button>
        </div>

      </div>
    </div>
  </AppLayout>
</template>
