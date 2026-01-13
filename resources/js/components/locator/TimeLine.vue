<script setup>
import { Check, Clock, X, Play } from 'lucide-vue-next'
import { usePage } from '@inertiajs/vue3'

const page = usePage()

const props = defineProps({
  data: {
    type: Array,
    default: () => [],
  },
})

const statusColor = (status) => {
  switch ((status || '').toLowerCase()) {
    case 'approved':
      return 'bg-green-500'
    case 'pending':
      return 'bg-yellow-400'
    case 'rejected':
      return 'bg-red-500'
    default:
      return 'bg-gray-400'
  }
}
</script>

<template>
  <div class="p-6 bg-white shadow rounded-md">
    <h2 class="text-xl font-bold mb-8 text-gray-800">Application Timeline</h2>

    <div class="relative">
      <!-- Start Node (Applicant) -->
      <div class="flex items-start relative mb-8">
        <!-- Icon & Connector -->
        <div class="flex flex-col items-center relative">
          <div class="w-12 h-12 flex items-center justify-center rounded-full bg-green-500 text-white shadow border-4 border-white z-10">
            <Play class="w-5 h-5" />
          </div>
          <div class="absolute top-12 left-1/2 w-0.5 h-full bg-gray-300"></div>
        </div>

        <!-- Connector Line to Text -->
        <div class="ml-4 relative flex-1">
          <div class="absolute top-6 left-0 w-6 h-0.5 bg-gray-300"></div>
          <div class="pl-8">
            <p class="font-semibold text-gray-700">{{ page.props.auth.user.name }}</p>
            <p class="text-sm text-gray-500">(Applicant)</p>
            <p class="text-sm text-gray-500">
              {{ page.props.application?.updated_at ? new Date(page.props.application.updated_at).toLocaleString() : '-' }}
            </p>
          </div>
        </div>
      </div>

      <!-- Approvers -->
      <template v-for="(approver, index) in props.data" :key="approver.id">
        <div class="flex items-start relative mb-8">
          <!-- Icon & Vertical Connector -->
          <div class="flex flex-col items-center relative">
            <div
              class="w-12 h-12 flex items-center justify-center rounded-full text-white shadow border-4 border-white z-10"
              :class="statusColor(approver.pivot.status)"
            >
              <template v-if="(approver.pivot.status || '').toLowerCase() === 'approved'">
                <Check class="w-5 h-5" />
              </template>
              <template v-else-if="(approver.pivot.status || '').toLowerCase() === 'rejected'">
                <X class="w-5 h-5" />
              </template>
              <template v-else>
                <Clock class="w-5 h-5" />
              </template>
            </div>

            <!-- Vertical Connector -->
            <div
              v-if="index < props.data.length -1"
              class="absolute top-12 left-1/2 w-0.5 h-full"
              :class="statusColor(approver.pivot.status)"
            ></div>
          </div>

          <!-- Connector Line to Text -->
          <div class="ml-4 relative flex-1">
            <div class="absolute top-6 left-0 w-6 h-0.5" :class="statusColor(approver.pivot.status)"></div>
            <div class="pl-8">
              <p class="font-semibold text-gray-700">{{ approver.name }}</p>
              <p class="text-sm text-gray-500 capitalize">{{ approver.pivot.status || '—' }}</p>
              <p class="text-sm text-gray-500 capitalize">{{ approver.pivot.role || '—' }}</p>
              <p v-if="approver.pivot.remark" class="text-xs text-gray-400 mt-1">
                <b class="text-gray-700">Remark:</b> {{ approver.pivot.remark }}
              </p>
              <p v-if="approver.pivot.acted_at" class="text-xs text-gray-400 mt-1">
                {{ new Date(approver.pivot.acted_at).toLocaleString() }}
              </p>
            </div>
          </div>
        </div>
      </template>

      <!-- Empty state -->
      <div v-if="!props.data.length" class="text-gray-500 text-sm italic py-4 text-center">
        No approvers found.
      </div>
    </div>
  </div>
</template>
