<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/layouts/AppLayout.vue';

const showModal = ref(false)

const { forms } = defineProps<{
  forms: {
    id: number
    name: string
    description: string
    form_type: string
    form_user: string
    approver_group_id: number | null
    created_at: string
    updated_at: string
  }[]
}>()

// Create form (Inertia)
const createForm = useForm({
  name: '',
  description: '',
  form_type: '',
  form_user: '',
  approver_group_id: null as number | null,
})

const submit = () => {
    console.log(createForm);
  //createForm.post(route('forms.store'), {
   // onSuccess: () => {
      createForm.reset()
      showModal.value = false
    }
 const confirmDelete = (id: number) => {
  if (confirm('Are you sure you want to delete this form?')) {
    Inertia.delete(route('forms.destroy', id))
  }
} 

</script>

<template>
  <AppLayout>
  <div class="p-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Forms</h1>

      <button
        @click="showModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm"
      >
        + Add Form
      </button>
    </div>

    <!-- Table -->
    <table class="w-full border">
      <thead class="bg-gray-100">
        <tr>
          <th class="border p-2 text-center">Name</th>
          <th class="border p-2 text-center">Type</th>
          <th class="border p-2 text-center">User</th>
          <th class="border p-2 text-center">Approver Group</th>
          <th class="border p-2 text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="form in forms" :key="form.id">
          <td class="border p-2 text-center">{{ form.name }}</td>
          <td class="border p-2 text-center">{{ form.form_type }}</td>
          <td class="border p-2 text-center">{{ form.form_user }}</td>
          <td class="border p-2 text-center">{{ form.approver_group_id }}</td>
          <td class="border p-2 text-center">
            <div class="inline-flex">
    <!-- View -->
    <button
      class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 text-xs rounded-l"
      @click="$inertia.get(route('forms.show', form.id))"
    >
      View
    </button>

    <!-- Edit -->
    <button
      class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 text-xs border-l border-white"
      @click="$inertia.get(route('forms.edit', form.id))"
    >
      Edit
    </button>

    <!-- Delete -->
    <button
      class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 text-xs rounded-r border-l border-white"
      @click="confirmDelete(form.id)"
    >
      Delete
    </button>
  </div>
          </td>
        </tr>

        <tr v-if="forms.length === 0">
          <td colspan="5" class="p-4 text-center text-gray-500">
            No forms found.
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal -->
    <div
      v-if="showModal"
      class="fixed inset-0 z-50 flex items-center justify-center"
    >
      <!-- Backdrop -->
      <div
        class="absolute inset-0 bg-black/50"
        @click="showModal = false"
      ></div>

      <!-- Modal box -->
      <div class="relative bg-white rounded-lg shadow-lg w-full max-w-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Create Form</h2>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-4">

          <!-- Name -->
          <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input
              v-model="createForm.name"
              class="border rounded p-2 w-full"
              type="text"
            />
            <p v-if="createForm.errors.name" class="text-red-500 text-sm">
              {{ createForm.errors.name }}
            </p>
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium mb-1">Form Type</label>
            <select
              v-model="createForm.form_type"
              class="border rounded p-2 w-full"
            >
              <option value="">Select type</option>
              <option value="ATO">ATO</option>
               <option value="Permit">Permit</option>
              <option value="ACCREDITATION">Accreditation</option>
            </select>
            <p v-if="createForm.errors.form_type" class="text-red-500 text-sm">
              {{ createForm.errors.form_type }}
            </p>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-sm font-medium mb-1">Description</label>
            <textarea
              v-model="createForm.description"
              class="border rounded p-2 w-full"
              rows="3"
            ></textarea>
          </div>

          <!-- User -->
          <div>
  <label class="block text-sm font-medium mb-1">Form User</label>
  <select
    v-model="createForm.form_user"
    class="border rounded p-2 w-full"
  >
    <option value="">Select user type</option>
    <option value="Vendor">Vendor</option>
    <option value="Supplier">Supplier</option>
    <option value="Commercial">Commercial</option>
    <option value="Locator">Locator</option>
    <option value="All">All</option>
  </select>

  <p v-if="createForm.errors.form_user" class="text-red-500 text-sm">
    {{ createForm.errors.form_user }}
  </p>
</div>

          <!-- Approver Group -->
          <div>
  <label class="block text-sm font-medium mb-1">Select Approver Group</label>
  <select
    v-model="createForm.approver_group_id"
    class="border rounded p-2 w-full"
  >
    <option value="">Select approver group</option>
    <option value="ATO">ATO</option>
    <option value="SEZAD">SEZAD</option>
    <option value="ProvisionalGrant">ProvisionalGrant</option>
    <option value="Accreditation">Accreditation</option>
    <option value="Permit">Permit</option>
  </select>

  <p v-if="createForm.errors.approver_group_id" class="text-red-500 text-sm">
    {{ createForm.errors.approver_group_id }}
  </p>
</div>

          <!-- Actions -->
          <div class="flex justify-end gap-2 pt-4">
            <button
              type="button"
              @click="showModal = false"
              class="px-4 py-2 text-sm border rounded"
            >
              Cancel
            </button>

            <button
              type="submit"
              :disabled="createForm.processing"
              class="px-4 py-2 text-sm bg-blue-600 text-white rounded disabled:opacity-50"
            >
              Save
            </button>
          </div>

        </form>
      </div>
    </div>

  </div>
  </AppLayout>
</template>
