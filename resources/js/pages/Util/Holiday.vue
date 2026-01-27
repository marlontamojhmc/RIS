<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Inertia } from '@inertiajs/inertia'
import AppLayout from '@/layouts/AppLayout.vue';

const { holidays } = defineProps<{
  holidays: {
    id: number
    name: string
    date: string
    is_recurring: boolean
    type: string
    notes: string
    created_at: string
    updated_at: string
  }[]
}>()

// Modal visibility
const showModal = ref(false)

// Form
const holidayForm = useForm({
  name: '',
  date: '',
  is_recurring: false,
  type: '',
  notes: ''
})

// Submit form
const submitHoliday = () => {
    console.log(holidayForm);
//   holidayForm.post(route('holidays.store'), {
//     onSuccess: () => {
//       holidayForm.reset()
//       showModal.value = false
//     }
//   })
}

// Delete holiday
const confirmDelete = (id: number) => {
  if (confirm('Are you sure you want to delete this holiday?')) {
    Inertia.delete(route('holidays.destroy', id))
  }
}
</script>

<template>
    <AppLayout>
  <div class="p-6">

    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
      <h1 class="text-xl font-semibold">Holidays</h1>
      <button
        @click="showModal = true"
        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm"
      >
        + Add Holiday
      </button>
    </div>

    <!-- Table -->
    <table class="w-full border">
      <thead class="bg-gray-100">
        <tr>
          <th class="border p-2 text-center">ID</th>
          <th class="border p-2 text-center">Name</th>
          <th class="border p-2 text-center">Date</th>
          <th class="border p-2 text-center">Recurring</th>
          <th class="border p-2 text-center">Type</th>
          <th class="border p-2 text-center">Notes</th>
          <th class="border p-2 text-center">Actions</th>
        </tr>
      </thead>

      <tbody>
        <tr v-for="holiday in holidays" :key="holiday.id">
          <td class="border p-2 text-center">{{ holiday.id }}</td>
          <td class="border p-2 text-center">{{ holiday.name }}</td>
          <td class="border p-2 text-center">{{ new Date(holiday.date).toLocaleDateString() }}</td>
          <td class="border p-2 text-center">{{ holiday.is_recurring ? 'Yes' : 'No' }}</td>
          <td class="border p-2 text-center">{{ holiday.type }}</td>
          <td class="border p-2 text-center">{{ holiday.notes }}</td>
          <td class="border p-2 text-center">
            <div class="inline-flex">
              <button
                class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 text-xs rounded-l"
                @click="Inertia.get(route('holidays.show', holiday.id))"
              >
                View
              </button>
              <button
                class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 text-xs"
                @click="Inertia.get(route('holidays.edit', holiday.id))"
              >
                Edit
              </button>
              <button
                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 text-xs rounded-r"
                @click="confirmDelete(holiday.id)"
              >
                Delete
              </button>
            </div>
          </td>
        </tr>
        <tr v-if="holidays.length === 0">
          <td colspan="7" class="p-4 text-center text-gray-500">
            No holidays found.
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
        <h2 class="text-lg font-semibold mb-4">Add Holiday</h2>

        <form @submit.prevent="submitHoliday" class="space-y-4">
          <!-- Name -->
          <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input
              v-model="holidayForm.name"
              type="text"
              class="border rounded p-2 w-full"
            />
            <p v-if="holidayForm.errors.name" class="text-red-500 text-sm">
              {{ holidayForm.errors.name }}
            </p>
          </div>

          <!-- Date -->
          <div>
            <label class="block text-sm font-medium mb-1">Date</label>
            <input
              v-model="holidayForm.date"
              type="date"
              class="border rounded p-2 w-full"
            />
            <p v-if="holidayForm.errors.date" class="text-red-500 text-sm">
              {{ holidayForm.errors.date }}
            </p>
          </div>

          <!-- Recurring -->
          <div class="flex items-center gap-2">
            <input
              type="checkbox"
              v-model="holidayForm.is_recurring"
              id="recurring"
              class="rounded"
            />
            <label for="recurring" class="text-sm">Recurring</label>
          </div>

          <!-- Type -->
          <div>
            <label class="block text-sm font-medium mb-1">Type</label>
            <input
              v-model="holidayForm.type"
              type="text"
              class="border rounded p-2 w-full"
            />
          </div>

          <!-- Notes -->
          <div>
            <label class="block text-sm font-medium mb-1">Notes</label>
            <textarea
              v-model="holidayForm.notes"
              class="border rounded p-2 w-full"
              rows="3"
            ></textarea>
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
              :disabled="holidayForm.processing"
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
