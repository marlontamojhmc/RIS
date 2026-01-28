<template>
    <AppLayout>
  <div class="p-4 space-y-6">

    <!-- Application Info -->
    <div class="bg-white shadow rounded p-4 space-y-3">
      <h2 class="text-xl font-bold">Edit Application</h2>

      <div>
        <label class="font-semibold">Form Title</label>
        <input
          v-model="formData.form_title"
          type="text"
          disabled
          class="border rounded w-full p-2 bg-gray-100"
        />
      </div>

      <div>
        <label class="font-semibold">Status</label>
        <input
          v-model="formData.status"
          type="text"
          disabled
          class="border rounded w-full p-2 bg-gray-100"
        />
      </div>

      <div>
        <label class="font-semibold">Price</label>
        <input
          v-model="formData.price"
          type="number"
          step="0.01"
          disabled
          class="border rounded w-full p-2 bg-gray-100"
        />
      </div>
    </div>

    <!-- Article Details -->
    <div class="bg-white shadow rounded p-4">
      <h3 class="text-lg font-semibold mb-2">Article Details</h3>

      <table class="w-full border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="border p-2 text-left">Description</th>
            <th class="border p-2 text-left">Marks & Number</th>
            <th class="border p-2 text-left">Qty</th>
            <th class="border p-2 text-left">Gross Weight</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="article in formData.article_details"
            :key="article.id"
          >
            <td class="border p-2">
              <input v-model="article.detailed_description_of_article" class="input" />
            </td>
            <td class="border p-2">
              <input v-model="article.marks_and_number" class="input" />
            </td>
            <td class="border p-2">
              <input v-model="article.qty" type="number" class="input" />
            </td>
            <td class="border p-2">
              <input v-model="article.gross_weight" type="number" step="0.01" class="input" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Selections -->
    <div class="bg-white shadow rounded p-4">
      <h3 class="text-lg font-semibold mb-2">Selections</h3>

      <table class="w-full border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="border p-2 text-left">Option</th>
            <th class="border p-2 text-left">Amount</th>
            <th class="border p-2 text-left">Expired At</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="selection in formData.selections"
            :key="selection.id"
          >
            <td class="border p-2">
              <select v-model="selection.option_id" class="input">
                <option disabled value="">Select option</option>
                <option
                  v-for="opt in appOptions"
                  :key="opt.id"
                  :value="opt.id"
                >
                  {{ opt.name }} — {{ opt.price }}
                </option>
              </select>
            </td>
            <td class="border p-2">
              <input v-model="selection.amount" type="number" step="0.01" class="input" />
            </td>
            <td class="border p-2">
              <input v-model="selection.Expired_at" type="datetime-local" class="input" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Uploads -->
    <div class="bg-white shadow rounded p-4">
      <h3 class="text-lg font-semibold mb-2">Uploads</h3>

      <table class="w-full border border-gray-300">
        <thead class="bg-gray-100">
          <tr>
            <th class="border p-2 text-left">File Name</th>
            <th class="border p-2 text-left">Description</th>
            <th class="border p-2 text-left">Path</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="upload in formData.uploads"
            :key="upload.id"
          >
            <td class="border p-2">
              <input v-model="upload.file_name" disabled class="input bg-gray-100" />
            </td>
            <td class="border p-2">
              <input v-model="upload.description" disabled class="input bg-gray-100" />
            </td>
            <td class="border p-2">
              <input v-model="upload.file_path" disabled class="input bg-gray-100" />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-2">
      <button
        @click="showTimeline = true"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        Show Progress
      </button>

      <button
        @click="save"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Save Changes
      </button>
    </div>

    <!-- Timeline Modal -->
    <div
      v-if="showTimeline"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
      <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">

        <!-- Modal Header -->
        <div class="flex justify-between items-center border-b p-4">
          <h3 class="text-lg font-semibold">Approval Timeline</h3>
          <button
            @click="showTimeline = false"
            class="text-gray-500 hover:text-black text-xl"
          >
            ✕
          </button>
        </div>

        <!-- Modal Body -->
        <div class="p-4">
          <TimeLine :data="approvers" />
        </div>

        <!-- Modal Footer -->
        <div class="border-t p-4 flex justify-end">
          <button
            @click="showTimeline = false"
            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
          >
            Close
          </button>
        </div>

      </div>
    </div>

  </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import TimeLine from '@/components/locator/TimeLine.vue'
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps<{
  application: any
  approverGroup: any
  approvers: any[]
  appOptions: any[]
}>()

const formData = reactive(
  JSON.parse(JSON.stringify(props.application))
)

const showTimeline = ref(false)

function save() {
  router.post(
    `/applications/${formData.id}/update`,
    formData,
    { preserveScroll: true }
  )
}
</script>

<style scoped>

</style>
