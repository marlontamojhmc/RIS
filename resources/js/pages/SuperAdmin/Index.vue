<script setup lang="ts">
import { computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { Button } from '@/components/ui/button'
import EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import AppLayout from '@/layouts/AppLayout.vue'

// -----------------------
// Props from Inertia
// -----------------------
interface User {
  id: number
  name: string
  email: string
}

interface Pagination {
  current_page: number
  last_page: number
  per_page: number
  total: number
}

// Props passed from controller
const props = defineProps<{
  users: User[]
  pagination: Pagination
}>()

// Users table rows
const users = computed(() => props.users || [])

// Pagination metadata
const pagination = computed(() => props.pagination || {})

// Table headers
const headers = [
  { text: 'ID', value: 'id', sortable: true },
  { text: 'Name', value: 'name', sortable: true },
  { text: 'Email', value: 'email' },
  { text: 'Actions', value: 'actions' }
]

// Row actions
const viewUser = (id: number) => Inertia.get(`/superadmin/users/${id}`)
const editUser = (id: number) => Inertia.get(`/superadmin/users/${id}/edit`)
const deleteUser = (id: number) => {
  if (!confirm('Are you sure you want to delete this user?')) return

  fetch(`/superadmin/users/${id}`, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN':
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    }
  }).then(() => location.reload())
}
</script>

<template>
    <AppLayout>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-4">Users</h1>

    <!-- Users table -->
    <EasyDataTable :headers="headers" :items="users">
      <template #item-actions="{ item }">
        <div class="flex gap-2">
          <Button size="sm" variant="outline" @click="viewUser(item.id)">View</Button>
          <Button size="sm" variant="secondary" @click="editUser(item.id)">Edit</Button>
          <Button size="sm" variant="destructive" @click="deleteUser(item.id)">Delete</Button>
        </div>
      </template>
    </EasyDataTable>

    <!-- Pagination -->
    <div v-if="pagination.total" class="mt-4 flex items-center gap-2 text-sm text-muted-foreground">
      <span>Page {{ pagination.current_page }} of {{ pagination.last_page }} | Total: {{ pagination.total }} users</span>

      <Button
        size="sm"
        variant="outline"
        :disabled="pagination.current_page <= 1"
        @click="Inertia.get(`/superadmin/users?page=${pagination.current_page - 1}`)"
      >
        Previous
      </Button>

      <Button
        size="sm"
        variant="outline"
        :disabled="pagination.current_page >= pagination.last_page"
        @click="Inertia.get(`/superadmin/users?page=${pagination.current_page + 1}`)"
      >
        Next
      </Button>
    </div>
  </div>
    </AppLayout>
</template>
