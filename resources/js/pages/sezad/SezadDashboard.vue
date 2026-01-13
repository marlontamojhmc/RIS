<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ChevronDown } from 'lucide-vue-next'
import { Link, usePage } from '@inertiajs/vue3'
import type { PageProps } from '@inertiajs/core'
import { ref, computed, reactive } from 'vue'
import Vue3EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import axios from 'axios'

/* ---------- types ---------- */
interface UserDetails {
  permission_id: number
  role_id: number
  department_id: number
  division_id: number
  user_function_id: number
}

interface AuthUser {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
  details?: UserDetails
}

interface TempUser {
  id: number
  name: string
  email: string
  business_type: string | null
  locator: string[] | null
  status: string | null
  remark: string | null
  created_at: string
}

interface CustomPageProps extends PageProps {
  auth: { user: AuthUser }
  usersTemp: TempUser[]
  businessTypes: { id: number; name: string; description: string }[]
}

/* ---------- props / initial data ---------- */
const page = usePage<CustomPageProps>()
const user = page.props.auth.user
const usersTemp = ref<TempUser[]>(page.props.usersTemp || [])
const businessTypes = ref(page.props.businessTypes || [])

/* normalize locator to arrays (run once) */
usersTemp.value = usersTemp.value.map(u => ({
  ...u,
  locator: Array.isArray(u.locator) ? u.locator : (() => {
    try { return JSON.parse(u.locator as any || '[]') } catch { return [] }
  })()
}))

/* ---------- helpers ---------- */
const businessTypeMap = computed(() =>
  Object.fromEntries(businessTypes.value.map(bt => [bt.id, bt.description]))
)

/* Nav & Access */
const openDropdown = ref<string | null>(null)
function toggleDropdown(name: string) {
  openDropdown.value = openDropdown.value === name ? null : name
}

const hasAccess = (user: AuthUser, functionId: number) => {
  const d = user.details
  return d && d.role_id === 2 && d.permission_id === 2 && d.department_id === 12 && d.division_id === null && d.user_function_id === functionId
}

const accreditationCenter = hasAccess(user, 1)
const customsClearanceCenter = hasAccess(user, 2)
const laborCenter = hasAccess(user, 3)
const oneStopActionCenter = hasAccess(user, 4)
const sezadManager = hasAccess(user, 5)

interface NavLink { name: string; href?: string; children?: NavLink[] }

const fullNavItems: NavLink[] = [
  { name: "Accreditation Center", href: "sezad/accreditation" },
  { name: "Authority to Operate", href: "/authority-to-operate" },
  {
    name: "One Stop Action Center",
    children: [
      {
        name: "Clearances",
        children: [
          { name: "Gate Clearance", href: "/clearance/gatepass" },
          { name: "Bring-In Clearance", href: "/sezad/clearances/bring-in" },
          { name: "Bring-Out Clearance", href: "/clearance/bring-out" },
          { name: "Temporary Bring-Out Clearance", href: "/clearance/temp-bring-out" },
          { name: "Local Purchase Clearance", href: "/clearance/temp-bring-out" },
          { name: "Storage Clearance", href: "/clearance/storage" },
          { name: "Workflow Access Clearance", href: "/clearance/workflow" }
        ]
      }
    ]
  },
  {
    name: "Custom Clearance Center", children: [
      {
        name: "Clearances", children: [
          { name: "Gate Clearance", href: "/clearance/gatepass" },
          { name: "Bring-In Clearance", href: "/sezad/clearances/bring-in" },
          { name: "Bring-Out Clearance", href: "/clearance/bring-out" },
          { name: "Temporary Bring-Out Clearance", href: "/clearance/temp-bring-out" },
          { name: "Local Purchase Clearance", href: "/clearance/temp-bring-out" },
          { name: "Storage Clearance", href: "/clearance/storage" },
          { name: "Workflow Access Clearance", href: "/clearance/workflow" },
        ]
      }
    ]
  },
  {
    name: "Labor Center", children: [
      {
        name: "Clearances", children: [
          { name: "Gate Clearance", href: "/clearance/gatepass" },
          { name: "Bring-In Clearance", href: "/sezad/clearances/bring-in" },
          { name: "Bring-Out Clearance", href: "/clearance/bring-out" },
          { name: "Temporary Bring-Out Clearance", href: "/clearance/temp-bring-out" },
          { name: "Local Purchase Clearance", href: "/clearance/temp-bring-out" },
          { name: "Storage Clearance", href: "/clearance/storage" },
          { name: "Workflow Access Clearance", href: "/clearance/workflow" },
        ]
      }
    ]
  }
]

let navItems: NavLink[] = sezadManager ? fullNavItems : (oneStopActionCenter ? fullNavItems : [])

/* ---------- table / modal state ---------- */
const headers = [
  { text: "Name", value: "name", sortable: true },
  { text: "Email", value: "email", sortable: true },
  { text: "Business Type", value: "business_type", sortable: true },
  { text: "Locator", value: "locator", sortable: true },
  { text: "Created At", value: "created_at", sortable: true },
  { text: "Status", value: "status", sortable: true },
  { text: "Remarks", value: "remark", sortable: true }
]

const searchQuery = ref('')
const selectedUser = ref<TempUser | null>(null)
const loading = ref(false)

const isRemarkValid = computed(() =>
  (selectedUser.value?.remark ?? '').toString().trim().length > 0
)

/* filtered users computed - keeps locator normalized */
const filteredUsers = computed(() => {
  const normalized = usersTemp.value.map(u => ({
    ...u,
    locator: Array.isArray(u.locator) ? u.locator : (() => {
      try { return JSON.parse(u.locator as any || '[]') } catch { return [] }
    })()
  }))
  if (!searchQuery.value) return normalized
  const q = searchQuery.value.toLowerCase()
  return normalized.filter(u =>
    Object.values(u).some(v => String(v).toLowerCase().includes(q))
  )
})

/* ---------- actions ---------- */

/**
 * Open modal by clicking row.
 * We assign a shallow copy so modal edits don't mutate UI immediately.
 */
const openRemarksModal = (row: TempUser) => {
  selectedUser.value = { ...row }

  remarks.value = row.remark ?? ""

  // NEW LOGIC
  if (row.status === "new" || row.status === "New") {
    isEditingAllowed.value = true
  } else {
    isEditingAllowed.value = false
  }

  isModalOpen.value = true
}

/** Close modal */
const closeModal = () => {
  selectedUser.value = null
}

/**
 * Update status safely.
 * Capture the user reference at start to avoid null issues.
 */
const updateStatus = async (status: string) => {
  const user = selectedUser.value
  if (!user) {
    alert("No user selected.")
    return
  }

  loading.value = true

  try {
    // Keep payload local
    const payload = {
      id: user.id,
      status,
      remark: user.remark ?? ''
    }

    const res = await axios.post('/sezad/temp-users/update', payload)

    // Validate response
    const updated: TempUser | undefined = res?.data?.user
    if (!updated || !updated.id) {
      const serverMsg = res?.data?.message ?? JSON.stringify(res?.data ?? {})
      throw new Error("Invalid server response: " + serverMsg)
    }

    // Find and replace in local table by id
    const idx = usersTemp.value.findIndex(u => u.id === updated.id)
    if (idx !== -1) {
      usersTemp.value[idx] = updated
    } else {
      // If not found, push (defensive)
      usersTemp.value.push(updated)
    }

    closeModal()
    window.location.reload();
  } catch (err: any) {
    // axios errors include err.response
    if (err?.response) {
      console.error("Server error:", err.response.data)
      const serverMessage = err.response.data?.message || JSON.stringify(err.response.data)
      alert("Update failed: " + serverMessage)
    } else {
      console.error("Failed to update user:", err)
      alert("Failed to update user: " + (err?.message ?? 'Unknown error'))
    }
  } finally {
    loading.value = false
  }
}

/* status pill class */
const statusClass = (status = '') => {
  switch (status?.toLowerCase()) {
    case 'approved': return 'bg-green-100 text-green-700'
    case 'disapproved': return 'bg-red-200 text-red-700'
    case 'new': return 'bg-blue-100 text-blue-700'
    default: return 'bg-gray-200 text-gray-700'
  }
}


const remarks = ref("")
const isEditingAllowed = ref(false)
const isModalOpen = ref(false)

const isRemarksDisabled = computed(() => {
  if (!selectedUser.value) return true
  return selectedUser.value.status === 'approved' || selectedUser.value.status === 'disapproved'
})

// Approve button logic
const isApproveDisabled = computed(() => {
  if (!selectedUser.value) return true
  if (selectedUser.value.status === 'new') {
    return !((selectedUser.value.remark ?? '').trim().length > 0) || loading.value
  }
  return true // already approved/disapproved
})

// Disapprove button logic
const isDisapproveDisabled = computed(() => {
  if (!selectedUser.value) return true
  if (selectedUser.value.status === 'new') {
    return !((selectedUser.value.remark ?? '').trim().length > 0) || loading.value
  }
  return true // already approved/disapproved
})
</script>

<template>
  <AppLayout>
    <!-- Navigation Header (kept) -->
    <nav class="sticky top-0 left-0 right-0 bg-white shadow-md z-40">
      <div class="max-w-7xl mx-auto flex items-center justify-between px-4 py-3">
        <h1 class="text-xl font-bold text-blue-700">
          <Link href="/sezad">SEZAD</Link>
        </h1>

        <div class="hidden md:flex items-center space-x-4">
          <div v-for="item in fullNavItems" :key="item.name" class="relative">
            <Link v-if="!item.children" :href="item.href">{{ item.name }}</Link>

            <div v-else>
              <button @click="toggleDropdown(item.name)" class="flex items-center gap-1">
                {{ item.name }}
                <ChevronDown class="w-4 h-4" :class="{ 'rotate-180': openDropdown === item.name }" />
              </button>
              <div v-show="openDropdown === item.name" class="absolute bg-white shadow-lg mt-2 p-2 rounded">
                <template v-for="child in item.children">
                  <div v-if="child.children">
                    <p class="font-semibold">{{ child.name }}</p>
                    <ul>
                      <li v-for="sub in child.children" :key="sub.name">
                        <Link :href="sub.href">{{ sub.name }}</Link>
                      </li>
                    </ul>
                  </div>
                  <Link v-else :href="child.href">{{ child.name }}</Link>
                </template>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main -->
    <main class="p-6 bg-gray-100 min-h-screen">


      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">

        <!-- Title -->
        <h1 class="text-2xl font-bold text-[#0f75bc] mb-6">SEZAD – New Users Application</h1>

        <!-- Search Bar -->
        <input v-model="searchQuery" type="text" placeholder="Search..."
          class="border border-gray-300 text-black placeholder-gray-500 px-3 py-2 rounded-lg w-full md:w-64 focus:ring-2 focus:ring-blue-400 focus:outline-none" />

      </div>
      <!-- Table -->
      <EasyDataTable :headers="headers" :items="filteredUsers" :rows-per-page="10" show-index clickable
        @click-row="openRemarksModal" class="rounded-lg shadow border border-gray-200">
        <template #item-status="{ item, status }">
          <span :class="[statusClass(status), 'px-2 py-1 rounded-full text-xs font-medium']">{{ status }}</span>
        </template>

        <template #item-locator="{ item, locator }">
          {{ Array.isArray(locator) ? locator.join(', ') : (locator || '-') }}
        </template>

        <template #item-business_type="{ item, business_type }">
          {{ businessTypeMap[business_type] || '-' }}
        </template>

        <template #item-remark="{ item, remark }">
          {{ remark || '-' }}
        </template>
      </EasyDataTable>

      <!-- Modal -->
      <transition name="fade">
        <div v-if="selectedUser" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div class="bg-white p-6 rounded-2xl shadow-2xl w-full max-w-md relative">
            <!-- optional overlay spinner inside modal -->
            <div v-if="loading" class="absolute inset-0 bg-white/60 flex items-center justify-center rounded-2xl z-50">
              <div class="w-10 h-10 border-4 border-gray-300 border-t-blue-600 rounded-full animate-spin"></div>
            </div>

            <h2 class="text-xl font-bold mb-4 text-center">Update Status for {{ selectedUser.name }}</h2>

            <label class="block text-sm font-medium mb-2">Remarks</label>
            <textarea v-model="selectedUser.remark" rows="4" :disabled="isRemarksDisabled" 
            class="w-full border p-2 rounded 
              disabled:bg-gray-100 
              disabled:opacity-50 
              disabled:cursor-not-allowed">
            </textarea>

            <div class="flex justify-end space-x-3 mt-4">
              <button @click="closeModal"
                class="w-full border p-2 rounded bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">Close</button>

              <button @click="updateStatus('Disapproved')" :disabled="isDisapproveDisabled"
                class="px-4 py-2 rounded text-white 
                disabled:bg-red-300 
                disabled:cursor-not-allowed 
                bg-red-500"
                :class="(!isRemarkValid || loading) ? 'bg-red-200 cursor-not-allowed' : 'bg-red-400'">
                <span v-if="loading">Processing...</span>
                <span v-else>Disapprove</span>
              </button>

              <button @click="updateStatus('approved')" :disabled="isDisapproveDisabled"
                class="px-4 py-2 rounded text-white 
                disabled:bg-green-300 
                disabled:cursor-not-allowed 
                bg-green-500"
                :class="(!isRemarkValid || loading) ? 'bg-green-200 cursor-not-allowed' :  'bg-green-400'">
                <span v-if="loading">Processing...</span>
                <span v-else>Approve</span>
              </button>
            </div>
          </div>
        </div>
      </transition>
    </main>
  </AppLayout>
</template>

<style scoped>
.easy-data-table {
  --easy-table-header-bg: #f1f5f9;
  --easy-table-header-color: #0f75bc;
}
</style>
