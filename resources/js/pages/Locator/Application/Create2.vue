<script setup>
import { useForm } from '@inertiajs/vue3'
import Input from '@/components/ui/input/Input.vue'
import Label from '@/components/ui/label/Label.vue'
import Button from '@/components/ui/button/Button.vue'
import { ref } from 'vue'

// Available form types
const formTypes = ['ATO', 'Gate Pass', 'Bring-in']
const selectedType = ref(formTypes[0])

// Dynamic rows for the repeater
const form = useForm({
  rows: [
    { field1: '', field2: '', field3: '' }
  ]
})

// Add/remove row functions
const addRow = () => {
  form.rows.push({ field1: '', field2: '', field3: '' })
}

const removeRow = (index) => {
  form.rows.splice(index, 1)
}

// Switch form type
const switchFormType = (type) => {
  selectedType.value = type
  // Reset form rows if needed
  form.rows = [{ field1: '', field2: '', field3: '' }]
}

const submitForm = () => {
  form.post('/your-route')
}
</script>

<template>
  <div class="p-6 bg-white rounded-xl space-y-6">
    
    <!-- Form type buttons -->
    <div class="flex space-x-2 mb-4">
      <Button
        v-for="(type, idx) in formTypes"
        :key="idx"
        :class="{'bg-blue-600 py-10 px-10 rounded-full text-white': selectedType === type, 'bg-gray-200': selectedType !== type}"
        @click="switchFormType(type)"
      >
        {{ type }}
      </Button>
    </div>

    <!-- Repeater rows -->
    <div v-for="(row, rowIndex) in form.rows" :key="rowIndex" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
      <div class="space-y-1" v-for="(field, idx) in ['field1','field2','field3']" :key="idx">
        <Label>{{ 'Field ' + (idx + 1) }}</Label>
        <Input v-model="row[field]" :placeholder="'Enter field ' + (idx + 1)" />
      </div>
      <div class="flex space-x-2">
        <Button class="bg-green-600 hover:bg-green-700" @click="addRow">+</Button>
        <Button class="bg-red-500 hover:bg-red-600" @click="removeRow(rowIndex)" :disabled="form.rows.length === 1">-</Button>
      </div>
    </div>

    <Button class="mt-4" @click="submitForm">Submit</Button>
  </div>
</template>
