<script setup>
import { ref } from 'vue'

// Sample data — replace or fetch as needed
const approvers = ref([
  { id: 1, name: 'John Doe', status: 'Approved', date: '2025-10-12', remark: '' },
  { id: 2, name: 'Jane Smith', status: 'Pending', date: '2025-10-13', remark: '' },
  { id: 3, name: 'Michael Lee', status: 'Rejected', date: '2025-10-14', remark: 'Incomplete documents' },
])

// Track which approver is currently being "returned"
const activeRemarkId = ref(null)
const remarkText = ref('')

// Approve handler
const approve = (approver) => {
  approver.status = 'Approved'
  approver.date = new Date().toISOString().split('T')[0]
  approver.remark = ''
}

// Open remark input
const openRemarkInput = (approver) => {
  activeRemarkId.value = approver.id
  remarkText.value = approver.remark || ''
}

// Cancel remark input
const cancelRemark = () => {
  activeRemarkId.value = null
  remarkText.value = ''
}

// Submit remark
const submitRemark = (approver) => {
  approver.status = 'Rejected'
  approver.remark = remarkText.value.trim()
  approver.date = new Date().toISOString().split('T')[0]
  activeRemarkId.value = null
  remarkText.value = ''
}
</script>

<template>
  <div class="p-4 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Timeline Column -->
    <div>
      <h2 class="text-lg font-semibold mb-4">Approval Timeline</h2>
      <div class="relative border-l-2 border-gray-300">
        <div
          v-for="approver in approvers"
          :key="approver.id"
          class="mb-8 ml-4"
        >
          <!-- Dot -->
          <div
            class="absolute -left-2.5 mt-1.5 w-4 h-4 rounded-full border-2"
            :class="{
              'bg-green-500 border-green-500': approver.status === 'Approved',
              'bg-yellow-500 border-yellow-500': approver.status === 'Pending',
              'bg-red-500 border-red-500': approver.status === 'Rejected',
            }"
          ></div>

          <!-- Content -->
          <div class="bg-white p-4 rounded-lg shadow-sm">
            <div class="flex justify-between items-center mb-1">
              <h3 class="text-sm font-semibold">{{ approver.name }}</h3>
              <span class="text-xs text-gray-500">{{ approver.date }}</span>
            </div>
            <span
              class="inline-block px-2 py-0.5 rounded text-xs font-medium mb-1"
              :class="{
                'bg-green-100 text-green-800': approver.status === 'Approved',
                'bg-yellow-100 text-yellow-800': approver.status === 'Pending',
                'bg-red-100 text-red-800': approver.status === 'Rejected',
              }"
            >
              {{ approver.status }}
            </span>

            <!-- Show remark if returned -->
            <p v-if="approver.remark" class="mt-1 text-xs text-red-600 italic">
              Remark: {{ approver.remark }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Table Column -->
    <div>
      <h2 class="text-lg font-semibold mb-4">Approvers Table</h2>
      <div class="overflow-x-auto bg-white shadow-sm rounded-lg">
        <table class="min-w-full text-sm">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="px-4 py-2 text-left">Name</th>
              <th class="px-4 py-2 text-left">Status</th>
              <th class="px-4 py-2 text-left">Date</th>
              <th class="px-4 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="approver in approvers"
              :key="approver.id"
              class="border-b last:border-b-0 align-top"
            >
              <td class="px-4 py-2">{{ approver.name }}</td>
              <td class="px-4 py-2">
                <span
                  class="inline-block px-2 py-0.5 rounded text-xs font-medium"
                  :class="{
                    'bg-green-100 text-green-800': approver.status === 'Approved',
                    'bg-yellow-100 text-yellow-800': approver.status === 'Pending',
                    'bg-red-100 text-red-800': approver.status === 'Rejected',
                  }"
                >
                  {{ approver.status }}
                </span>
              </td>
              <td class="px-4 py-2 text-gray-500">{{ approver.date }}</td>
              <td class="px-4 py-2">
                <!-- Normal buttons -->
                <div v-if="activeRemarkId !== approver.id" class="space-x-2">
                  <button
                    class="px-2 py-1 bg-green-500 text-white rounded text-xs hover:bg-green-600"
                    @click="approve(approver)"
                  >
                    Approve
                  </button>
                  <button
                    class="px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600"
                    @click="openRemarkInput(approver)"
                  >
                    Return
                  </button>
                </div>

                <!-- Remark input box -->
                <div v-else class="space-y-1">
                  <textarea
                    v-model="remarkText"
                    rows="2"
                    class="w-full border rounded p-1 text-xs focus:ring focus:ring-red-200"
                    placeholder="Enter remark..."
                  ></textarea>
                  <div class="flex justify-end space-x-1">
                    <button
                      class="px-2 py-1 bg-gray-300 text-xs rounded hover:bg-gray-400"
                      @click="cancelRemark"
                    >
                      Cancel
                    </button>
                    <button
                      class="px-2 py-1 bg-red-500 text-white text-xs rounded hover:bg-red-600"
                      @click="submitRemark(approver)"
                    >
                      Save
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<style scoped>
@media (max-width: 640px) {
  .timeline-item {
    margin-left: 2rem;
  }
}
</style>
