<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import locatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue'
import { locator } from '@/routes'
import { type BreadcrumbItem } from '@/types'
import { Head, usePage, router } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import { Check, X, Eye } from 'lucide-vue-next'

/* ===============================
   PAGE
================================ */

const page = usePage()

/* ===============================
   FLASH MESSAGES
================================ */

const errorMessage = computed(() => page.props.flash?.error ?? null)
const successMessage = computed(() => page.props.flash?.success ?? null)

/* ===============================
   PROPS
================================ */

const props = defineProps<{
  vendor: any[]
}>()

/* ===============================
   BREADCRUMBS
================================ */

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Locator',
    href: locator.url(),
  },
]

/* ===============================
   COMPUTED
================================ */

const isEmpty = computed(() => props.vendor.length === 0)

/* ===============================
   MODAL STATE
================================ */

const showRemarkModal = ref(false)
const selectedVendor = ref<any | null>(null)
const remark = ref('')
const actionType = ref<'approve' | 'disapprove'>('approve')

/* ===============================
   VALIDATION
================================ */

const isRemarkValid = computed(() => remark.value.trim().length > 0)

/* ===============================
   ACTIONS
================================ */

const openRemarkModal = (item: any, type: 'approve' | 'Rejected') => {
  selectedVendor.value = item
  actionType.value = type
  remark.value = ''
  showRemarkModal.value = true
}

const closeRemarkModal = () => {
  showRemarkModal.value = false
  selectedVendor.value = null
  remark.value = ''
}

const submitAction = () => {
  if (!selectedVendor.value || !isRemarkValid.value) return

  router.post(
    `/locator/vendor/${selectedVendor.value.id}/approve`,
    {
      status: actionType.value === 'approve' ? 'Approved' : 'Rejected',
      remark: remark.value,
    },
    {
      preserveScroll: true,
      onSuccess: () => closeRemarkModal(),
    }
  )
}
</script>

<template>
  <Head title="Vendor Requests" />

  <locatorAppSidebarLayout :breadcrumbs="breadcrumbs">
    <h1 class="text-2xl font-bold mb-4 text-center">
      Vendor Requests
    </h1>

    <!-- FLASH MESSAGES -->
    <div
      v-if="errorMessage"
      class="mb-4 rounded text-center bg-red-100 border border-red-400 text-red-700 px-4 py-2"
    >
      {{ errorMessage }}
    </div>

    <div
      v-if="successMessage"
      class="mb-4 rounded text-center bg-green-100 border border-green-400 text-green-700 px-4 py-2"
    >
      {{ successMessage }}
    </div>

    <!-- TABLE -->
    <div class="mt-6 overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full text-sm border">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 border">#</th>
            <th class="px-3 py-2 border">Email</th>
            <th class="px-3 py-2 border">Business Name</th>
            <th class="px-3 py-2 border">Business Type</th>
            <th class="px-3 py-2 border">Status</th>
            <th class="px-3 py-2 border text-center">Action</th>
          </tr>
        </thead>

        <tbody>
          <tr v-if="isEmpty">
            <td colspan="6" class="text-center py-6 text-gray-500">
              No vendor requests found.
            </td>
          </tr>

          <tr
            v-else
            v-for="(item, index) in props.vendor"
            :key="item.id"
            class="hover:bg-gray-50"
          >
            <td class="px-3 py-2 border">{{ index + 1 }}</td>
            <td class="px-3 py-2 border">{{ item.email }}</td>
            <td class="px-3 py-2 border">{{ item.name }}</td>
            <td class="px-3 py-2 border">{{ item.business_type }}</td>
            <td class="px-3 py-2 border capitalize">{{ item.status }}</td>

            <td class="px-3 py-2 border">
              <div class="flex justify-center gap-2">
                <!-- APPROVE -->
                <button
                  @click="openRemarkModal(item, 'approve')"
                  class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700"
                >
                  <Check class="w-4 h-4" />
                </button>

                <!-- DISAPPROVE -->
                <button
                  @click="openRemarkModal(item, 'Rejected')"
                  class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700"
                >
                  <X class="w-4 h-4" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- REMARK MODAL -->
    <div
      v-if="showRemarkModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
      <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">
        <h2 class="text-lg font-semibold mb-2">
          {{ actionType === 'approve' ? 'Approve Vendor' : 'Reject Vendor' }}
        </h2>

        <p class="text-sm text-gray-600 mb-3">
          Vendor: <strong>{{ selectedVendor?.email }}</strong>
        </p>

        <textarea
          v-model="remark"
          rows="4"
          placeholder="Remark is required"
          class="w-full border rounded px-3 py-2 focus:ring focus:ring-blue-300"
        ></textarea>

        <p v-if="!isRemarkValid" class="text-xs text-red-500 mt-1">
          Remark is required.
        </p>

        <div class="flex justify-end gap-2 mt-4">
          <button
            @click="closeRemarkModal"
            class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300"
          >
            Cancel
          </button>

          <button
            @click="submitAction"
            :disabled="!isRemarkValid"
            :class="[
              'px-4 py-2 rounded text-white',
              actionType === 'approve'
                ? 'bg-green-600 hover:bg-green-700'
                : 'bg-red-600 hover:bg-red-700',
              !isRemarkValid && 'bg-gray-300 cursor-not-allowed'
            ]"
          >
            {{ actionType === 'approve' ? 'Approve' : 'Rejected' }}
          </button>
        </div>
      </div>
    </div>
  </locatorAppSidebarLayout>
</template>
