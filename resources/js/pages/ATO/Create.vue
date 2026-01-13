<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { ref } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'

// Business type radio value: "sole" or "partnership"
const businessType = ref('')
const page= usePage()
const file = ref(null)

// Form data
const form = useForm({
  application_date: '',
  application_type: '',
  corporate_name: '',
  owner_name: '',
  tin: '',
  owner_mobile: '',
  office_address: '',
  rep_mobile: '',
  owner_email: '',
  rep_email: '',
  trade_name: '',
  file: null,
})

// Dropdown options
const applicationTypes = [
  { label: 'New Application', value: 'new' },
  { label: 'Renewal', value: 'renewal' },
  { label: 'Update', value: 'update' },
]

// File input handler
function handleFileChange(event) {
  file.value = event.target.files[0]
  form.file = file.value
}

// Submit handler
function submit() {
  form.post('/ATO', {
    onStart: () => console.log('Submitting...'),
    onSuccess: () => {
      console.log('Success!');
      // do something after save
    },
    onError: (errors) => {
      console.log('Validation errors:', errors);
    },
    onFinish: () => console.log('Finished'),
  })
}

// Reset handler
function reset() {
  form.reset()
  file.value = null
  businessType.value = ''
}
const [appId] = page.props.applications
</script>

<template>
  <AppLayout>
    <div class="w-full p-6 max-w-4xl mx-auto">
      {{ appId.id }}
      <h1 class="text-2xl text-center font-semibold mb-6">ATO Registration</h1>

      <!-- Business Type Radios -->
      <div class="mb-4">
        <label class="flex items-center space-x-2 mb-2">
          <input
            type="radio"
            value="sole"
            v-model="businessType"
            class="w-4 h-4"
          />
          <span>Sole Proprietorship</span>
        </label>

        <label class="flex items-center space-x-2">
          <input
            type="radio"
            value="partnership"
            v-model="businessType"
            class="w-4 h-4"
          />
          <span>Partnership/Corporation/Cooperative</span>
        </label>
      </div>

      <form @submit.prevent="submit" novalidate>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

          <!-- Application Date -->
          <div>
            <Label class="block text-sm font-medium mb-1">Application Date</Label>
            <Input
              type="datetime-local"
              v-model="form.application_date"
              class="w-full"
            />
          </div>

          <!-- Application Type -->
          <div>
            <Label class="block text-sm font-medium mb-1">Type of Application</Label>
            <select
              v-model="form.application_type"
              class="w-full border rounded px-3 py-2"
            >
              <option value="" disabled>Select type</option>
              <option
                v-for="type in applicationTypes"
                :key="type.value"
                :value="type.value"
              >
                {{ type.label }}
              </option>
            </select>
          </div>

          <!-- Corporate Name (Partnership Only) -->
          <div v-show="businessType === 'partnership'">
            <Label class="block text-sm font-medium mb-1">Corporate Name</Label>
            <Input
              v-model="form.corporate_name"
              type="text"
              class="w-full"
              placeholder="Corporate Name"
            />
          </div>

          <!-- Owner Name (Sole Only) -->
          <div v-show="businessType === 'sole'">
            <Label class="block text-sm font-medium mb-1">Name of Owner</Label>
            <Input
              v-model="form.owner_name"
              type="text"
              class="w-full"
              placeholder="Owner Name"
            />
          </div>

          <!-- TIN -->
          <div>
            <Label class="block text-sm font-medium mb-1">TIN (Tax Identification Number)</Label>
            <Input
              v-model="form.tin"
              type="text"
              class="w-full"
              placeholder="Tax ID Number"
            />
          </div>

          <!-- Owner Mobile Number -->
          <div>
            <Label class="block text-sm font-medium mb-1">Mobile Number (Owner)</Label>
            <Input
              v-model="form.owner_mobile"
              type="text"
              class="w-full"
              placeholder="Owner Mobile Number"
            />
          </div>

          <!-- Address -->
          <div>
            <Label class="block text-sm font-medium mb-1">Principal Office Address</Label>
            <Input
              v-model="form.office_address"
              type="text"
              class="w-full"
              placeholder="Office Address"
            />
          </div>

          <!-- Representative Mobile -->
          <div>
            <Label class="block text-sm font-medium mb-1">Mobile Number (Representative)</Label>
            <Input
              v-model="form.rep_mobile"
              type="text"
              class="w-full"
              placeholder="Representative Mobile No."
            />
          </div>

          <!-- Owner Email -->
          <div>
            <Label class="block text-sm font-medium mb-1">Owner Email</Label>
            <Input
              v-model="form.owner_email"
              type="email"
              class="w-full"
              placeholder="owner@gmail.com"
            />
          </div>

          <!-- Representative Email -->
          <div>
            <Label class="block text-sm font-medium mb-1">Representative Email</Label>
            <Input
              v-model="form.rep_email"
              type="email"
              class="w-full"
              placeholder="representative@gmail.com"
            />
          </div>

          <!-- Trade Name -->
          <div>
            <Label class="block text-sm font-medium mb-1">Trade Name</Label>
            <Input
              v-model="form.trade_name"
              type="text"
              class="w-full"
              placeholder="Trade Name"
            />
          </div>

          <!-- File Upload: Sole -->
          <div v-show="businessType === 'sole'">
            <Label class="block text-sm font-medium mb-1">Attach DTI Certificate</Label>
            <input
              type="file"
              @change="handleFileChange"
              class="w-full border rounded px-3 py-2"
            />
            
          </div>

          <!-- File Upload: Partnership -->
          <div v-show="businessType === 'partnership'">
            <Label class="block text-sm font-medium mb-1">Attach SEC Certificate</Label>
            <input
              type="file"
              @change="handleFileChange"
              class="w-full border rounded px-3 py-2"
            />
           
          </div>

        </div>

        <!-- Buttons -->
        <div class="flex items-center gap-3 mt-6">
          <button
            type="submit"
            class="px-4 py-2 rounded bg-black text-white"
            :disabled="form.processing"
          >
            {{ form.processing ? "Sending..." : "Send" }}
          </button>

          <Button
            type="button"
            @click="reset"
            class="px-3 py-2 border rounded"
            :disabled="form.processing"
          >
            Reset
          </Button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
