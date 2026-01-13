<script setup lang="ts">
import { defineProps, defineEmits } from 'vue'
import { CircleCheck } from 'lucide-vue-next'

interface ArticleDetail {
  id: number
  application_form_id: number
  user_id: number
  marks_and_number: string
  qty: number
  detailed_description_of_article: string
  gross_weight: string
  created_at: string
  updated_at: string
  verified_at?: boolean // ✅ per-article flag
  status:string
}

const props = defineProps<{
  title?: string
  details: ArticleDetail[]
  showActions?: boolean
}>()

const emit = defineEmits(['view', 'edit', 'delete', 'verify'])
</script>

<template>
  <div class="w-full my-6 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
     {{ verified }}
    <div v-if="title" class="px-4 py-3 border-b border-gray-100 bg-gray-50">
      <h2 class="text-lg font-semibold text-gray-700">{{ title }}</h2>
    </div>

    <!-- Table -->
    <table class="min-w-full divide-y divide-gray-100">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Marks & Numbers</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Qty</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Description</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Gross Weight</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
          <th v-if="showActions" class="px-4 py-2 text-right text-sm font-medium text-gray-600">Actions</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-gray-100">
        <tr v-for="item in details" :key="item.id">
          <td class="px-4 py-2 text-sm text-gray-700">{{ item.marks_and_number }}</td>
          <td class="px-4 py-2 text-sm text-gray-700">{{ item.qty }}</td>
          <td class="px-4 py-2 text-sm text-gray-600">{{ item.detailed_description_of_article }}</td>
          <td class="px-4 py-2 text-sm text-gray-700">{{ item.gross_weight }}</td>
           <td class="px-4 py-2 text-sm text-gray-700">
            <CircleCheck
            v-if="item.status === 'Verified'"
            class="text-green-600 w-5 h-5"
            title="Verified"
          /></td>
          <td v-if="showActions" class="px-4 py-2 text-right flex items-center justify-end">
            <!-- Show Verify button if not verified -->
            <button
              v-if="!item.verified"
              class="ml-3 text-green-600 hover:underline text-sm"
              @click="$emit('verify', item)"
            >
              Verify
            </button>

            <!-- Show Check icon if verified -->
            <Check
              v-else
              class="text-green-600 w-5 h-5"
              title="Verified"
            />
          </td>
        </tr>

        <tr v-if="details.length === 0">
          <td colspan="6" class="px-4 py-3 text-center text-gray-400 text-sm">
            No article details found.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
