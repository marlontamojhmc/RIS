<script setup lang="ts">
import LocatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { locator } from '@/routes';
import applications from '@/routes/applications'
import ApplicationTable from '@/components/common/ApplicationTable.vue'
import TopCard from '@/components/common/TopCard.vue'
import { ref } from "vue"
import { router, usePage} from '@inertiajs/vue3'

const page =usePage()

const props = defineProps({
  applications: {
    type: Array,
    required: true,
    default: () => [],
  },
})
const status = {
  atoCertified: '',
  activeUsers: '',
  sezadRequests: '',
  bddUsers: '',
}
const app = page.props.applications[0] && !page.props.applications[0].status ? page.props.applications[0] : null
if (app?.status === 'Pending') {
  status.atoCertified = 'ATO Expired'
} else if (app?.status === 'Approved') {
  status.atoCertified = 'ATO Certified'
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Locator', href: locator.url() },
  { title: 'Create Permit', href: applications.create.url() },
  { title: 'Pending Applications', href: applications.pending.url() },
  { title: 'Approved Applications', href: applications.approved.url() },
]


// Optional table action handlers
function handleView(app: any) {
  router.visit(`/loctr/applications/${app.application_id}`)
}

function handleEdit(app: any) {
  console.log('Edit', app)
}

function handleDelete(app: any) {
  console.log('Delete', app)
}
</script>

<template>
  
  <LocatorAppSidebarLayout :breadcrumbs="breadcrumbs">
    <TopCard :stats="page.props.applications[0] && !page.props.applications[0].status ? page.props.applications[0] : null"/>
   
    <h1 class="text-2xl font-bold mb-4">Approved Application Lists</h1>

    <ApplicationTable
      :applications="props.applications"
      @view="handleView"
      @edit="handleEdit"
      @delete="handleDelete"
    />
  </LocatorAppSidebarLayout>
</template>
