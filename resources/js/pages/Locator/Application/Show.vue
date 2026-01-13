<script setup lang="ts">
import { ref } from 'vue'
import LocatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { locator } from '@/routes';
import applications from '@/routes/applications'
import { router, usePage,useForm } from '@inertiajs/vue3'
import TimeLine from '@/components/locator/TimeLine.vue'
import UploadsTable from '@/components/locator/UploadsTable.vue'
import ArticleDetailTable from '@/components/locator/ArticleDetailTable.vue'
const page = usePage()
// Base URL (safe for SSR)
const baseUrl = typeof window !== 'undefined' ? window.location.origin : ''

// Props definition
const props = defineProps<{
  application: {
    id: number
    status: string
    form_title: string
    user_id: number
    control_number: string | null
    form_number: string
    created_at: string
    updated_at: string
    article_details: any[]
    uploads: any[]
    selections: any[]
  }
  approverGroup: {
    id: number
    name: string
    description: string | null
  } | null
  approvers: {
    id: number
    name: string
    email: string
    role: string | null
    sequence: number
    status: string | null
    acted_at: string | null
    remark: string | null
  }[]
}>()

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Locator', href: locator.url() },
  { title: 'Applications', href: applications.index.url() },
  { title: `Application #${props.application.id}`, href: '#' },
]

// Make approvers reactive & editable
const approvers = ref([...props.approvers])

// Modal state for "Return"
const showReturnModal = ref(false)
const returnRemark = ref('')
const currentApproverId = ref<number | null>(null)

// Check if approver can act (approve/return)
const canAct = (approver: any) => {

  if (!approver?.pivot) return false
  // if already approved, skip
  if (approver.pivot.status === 'Approved') return false

  // if first in sequence
  if (approver.pivot.sequence === 1) return true

  // find previous approver
  const prev = approvers.value.find(a => a.pivot.sequence === approver.pivot.sequence - 1)
  return prev ? prev.pivot.status === 'Approved' : false
}
//Verify Single Article Detail
const VerifyArticle = (details) =>{
  router.post(`/loctr/articles/${details.id}/verify`,{},{
    onSuccess: () =>{
      console.log(`Article ${details.id} verified successfully`)
    },
    onError: (errors) =>{
      console.error(errors)
    }
  })
}
//Verify Single Supporting Document
const ValidateDocument = (file) => {
  router.post(`/loctr/uploads/${file.id}/verify`, {}, {
    onSuccess: () => {
      console.log(`Document ${file.id} verified successfully`)
    },
    onError: (errors) => {
      console.error(errors)
    }
  })
}
// Approve action
const handleApprove = (approverId: number) => {
  router.post(
    `/application-for-approval/${props.application.form_number}/approvers/${approverId}/approve`,
    {},
    {
      onSuccess: (page) => {
        console.log(page.props.flash?.success)
        approvers.value = page.props.approvers ?? []
      },
    }
  )
}

// Show return modal
const handleReturnClick = (approverId: number) => {
  currentApproverId.value = approverId
  returnRemark.value = ''
  showReturnModal.value = true
}

const form = useForm({
  remark: '',
  currentApproverId: null, 
  form_number:null,
})

const submitReturn = () => {
  if (!currentApproverId.value) return
  form.currentApproverId = currentApproverId.value
  form.form_number = props.application.form_number
  form.post(
    `/application-for-approval/${props.application.form_number}/approvers/${currentApproverId.value}/return`,
    {
      onSuccess: (page) => {
        console.log(returnRemark)
        const idx = approvers.value.findIndex(a => a.id === currentApproverId.value)
        if (idx !== -1) {
          approvers.value[idx].pivot.status = page.props.flash?.success || 'Returned'
          approvers.value[idx].pivot.remark = page.props.flash?.remark
        }

        showReturnModal.value = false
        form.reset()
      },
    }
  )
  }
</script>

<template>
  <LocatorAppSidebarLayout :breadcrumbs="breadcrumbs" >
    <!-- Title -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
        {{ props.application.form_title }}
      </h1>
      <pre>Current LoggedIn User ID: {{ page.props.auth.user.id }}</pre>
      <p class="text-gray-600 dark:text-gray-400">
        Application #{{ props.application.id }} • Status:
        <span
          :class="{
            'text-yellow-600 dark:text-yellow-400': props.application.status === 'Pending',
            'text-green-600 dark:text-green-400': props.application.status === 'Approved',
            'text-red-600 dark:text-red-400': props.application.status === 'Returned',
          }"
        >
          {{ props.application.status.charAt(0).toUpperCase() + props.application.status.slice(1) }}
        </span>
      </p>
    </div>
   
    <!-- Basic Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
      <div
        class="p-4 border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-sm"
      >
        <h2 class="font-semibold mb-2 text-gray-900 dark:text-gray-100">Basic Information</h2>
        <ul class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
          <li><strong>Form Number:</strong> {{ props.application.form_number }}</li>
          <li><strong>Control Number:</strong> {{ props.application.control_number ?? '—' }}</li>
          <li><strong>User ID:</strong> {{ props.application.user_id }}</li>
          <li>
            <strong>Created At:</strong>
            {{ new Date(props.application.created_at).toLocaleString() }}
          </li>
          
        </ul>
      </div>
    </div>
   
    <ArticleDetailTable
    title="Article Details"
    :details="props.application.article_details"
    :showActions="true"
    @view=""
    @edit=""
    @delete=""
    @verify="VerifyArticle"
  />

    <!---Uploads-->
   
   <UploadsTable
    title="Uploaded Supporting Document/s"
    :files="props.application.uploads"
    :showActions="true"
    @view=""
    @delete=""
    @validate="ValidateDocument"
  />
    <!-- Approver Group -->
    <div v-if="props.approverGroup" class="mb-8 hidden">
      <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Approver Group</h2>
      <div
        class="p-4 border rounded-lg bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 shadow-sm"
      >
        <p class="text-sm text-gray-700 dark:text-gray-300">
          <strong>Name:</strong> {{ props.approverGroup.name }}
        </p>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          <strong>Description:</strong> {{ props.approverGroup.description ?? '—' }}
        </p>
      </div>
    </div>

    <!-- Approvers Table -->
    <div v-if="approvers.length">
      <h2 class="text-lg font-semibold mb-2 text-gray-900 dark:text-gray-100">Approvers</h2>
      <div
        class="overflow-x-auto border rounded-lg border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
      >
        <table class="min-w-full text-sm">
          <thead
            class="bg-gray-100 dark:bg-gray-700 text-left text-gray-800 dark:text-gray-200"
          >
            <tr>
              <th class="p-2">#</th>
              <th class="p-2">Name</th>
              <th class="p-2">Email</th>
              <th class="p-2">Role</th>
              <th class="p-2">Status</th>
              <th class="p-2">Remark</th>
              <th class="p-2">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 dark:text-gray-300">
            <tr
              v-for="(approver, index) in approvers"
              :key="approver.id"
              class="border-t border-gray-200 dark:border-gray-700"
            >
              <td class="p-2">{{ index + 1 }}</td>
              <td class="p-2">{{ approver.name }}</td>
              <td class="p-2">{{ approver.email }}</td>
              <td class="p-2">{{ approver.pivot.role ?? 'N/A' }}</td>
             
              <td class="p-2">
                <span
                  :class="{
                    'text-yellow-600 dark:text-yellow-400': approver.pivot.status === 'Pending',
                    'text-green-600 dark:text-green-400': approver.pivot.status === 'Approved',
                    'text-red-600 dark:text-red-400': approver.pivot.status === 'Returned',
                  }"
                >
                  {{ approver.pivot.status ?? 'Waiting for Approval' }}
                </span>
              </td>
              <td class="p-2">{{ approver.pivot.remark }}</td>
              <td class="p-2 flex gap-2">
                <button
  v-if=" canAct(approver)"
  @click="handleApprove(approver.id)"
  class="px-3 py-1 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-medium"
>

  Approve
</button>


                <button
                  v-if="canAct(approver)"
                  @click="handleReturnClick(approver.id)"
                  class="px-3 py-1 rounded bg-red-600 hover:bg-red-700 text-white text-xs font-medium"
                >
                  Return
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Return Modal -->
    <div
      v-if="showReturnModal"
      class="fixed inset-0 flex items-center justify-center z-50 bg-black/50"
    >
      <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-96">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
          Return Remark
        </h3>
        <input type="hidden" 
        value="456" />
        <textarea
          v-model="form.remark"
          placeholder="Enter remark..."
          class="w-full p-2 border border-gray-300 dark:border-gray-600 rounded mb-4 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
        ></textarea>
        <div class="flex justify-end gap-2">
          <button
            class="px-4 py-2 rounded bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-gray-100"
            @click="showReturnModal = false"
          >
            Cancel
          </button>
          <button
            class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white"
            @click="submitReturn"
          >
            Submit
          </button>
        </div>
      </div>
    </div>
   
    <TimeLine :data="props.approvers" />
  </LocatorAppSidebarLayout>
</template>
