<script setup lang="ts">
import { defineProps } from 'vue'

const props = defineProps<{
  title?: string
  files: Array<{
    id: number | string
    file_name: string
    file_path: string
    file_type: string
    file_size: number
    description?: string | null
    created_at?: string
  }>
  showActions?: boolean
}>()

// Format file size nicely
const formatSize = (bytes: number) => {
  if (!bytes) return '0 B'
  const units = ['B', 'KB', 'MB', 'GB']
  let i = 0
  while (bytes >= 1024 && i < units.length - 1) {
    bytes /= 1024
    i++
  }
  return `${bytes.toFixed(1)} ${units[i]}`
}
</script>

<template>
  <div class="w-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div v-if="title" class="px-4 py-3 border-b border-gray-100 bg-gray-50">
      <h2 class="text-lg font-semibold text-gray-700">{{ title }}</h2>
    </div>

    <!-- Table -->
    <table class="min-w-full divide-y divide-gray-100">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Filename</th>
          <!-- <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Type</th>
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Size</th> -->
          <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Uploaded</th>
          <th v-if="showActions" class="px-4 py-2 text-right text-sm font-medium text-gray-600">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <tr v-for="file in files" :key="file.id">
          <td class="px-4 py-2 text-sm text-gray-700">
            <a
              :href="`/storage/${file.file_path}`"
              target="_blank"
              class="text-blue-600 hover:underline"
            >
              {{ file.file_name }}
            </a>
          </td>
          <!-- <td class="px-4 py-2 text-sm text-gray-500">{{ file.file_type }}</td>
          <td class="px-4 py-2 text-sm text-gray-500">{{ formatSize(file.file_size) }}</td> -->
          <td class="px-4 py-2 text-sm text-gray-500">
            {{ new Date(file.created_at).toLocaleString() }}
          </td>
           <td v-if="showActions" class="px-4 py-2 text-right">
            <!--<button
              class="text-blue-600 hover:underline text-sm"
              @click="$emit('view', file)"
            >
              View
            </button> -->
            <!-- <button
              class="ml-3 text-red-600 hover:underline text-sm"
              @click="$emit('delete', file)"
            >
              Delete
            </button> -->
            <button
              class="ml-3 text-red-600 hover:underline text-sm"
              @click="$emit('validate', file)"
            >
              Verify
            </button>
          </td>
        </tr>
        <tr v-if="files.length === 0">
          <td colspan="5" class="px-4 py-3 text-center text-gray-400 text-sm">
            No uploaded files found.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
