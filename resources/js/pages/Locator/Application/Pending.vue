<script setup lang="ts">
import LocatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { locator } from '@/routes';
import applications from '@/routes/applications'
import ApplicationTable from '@/components/common/ApplicationTable.vue'
import TopCard from '@/components/common/TopCard.vue'
import { router, usePage } from '@inertiajs/vue3'


const page = usePage()
//import Approver from '@/components/locator/Approver.vue'

const props = defineProps({
  applications: {
    type: Array,
    required: true,
    default: () => [],
  },
  ATO: {
    type: String,
    required: true,
    default: '', // optional default
  }
})
const stats = {
  atoCertified: '',
  activeUsers: '',
  sezadRequests: '',
  bddUsers: '',
}
const ato = props.ATO?.[0]?.status ?? "";
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Locator', href: locator.url() },
  { title: 'Create Permit', href: '#' },
  { title: 'Pending Application', href: '/' },
]

// Optional table action handlers
function handleView(app: any) {
 const formNumber =
    app.form_number ??
    app.application?.form_number ??
    ''
    console.log(app);
  const upper = String(formNumber).toUpperCase()
  if (upper.startsWith('VA')) {
    router.visit(`/vendor/accreditations/${app.id}`)
   
  }else{
  router.visit(`/loctr/applications/${app.application_id}`)
  }
}

function handleEdit(app: any) {
  router.visit(`/applications/${app.application_id}/edit`)
  
}

function handleDelete(app: any) {
  console.log('Delete', app)
}
</script>

<template>
  <LocatorAppSidebarLayout :breadcrumbs="breadcrumbs" :app-id="10">
    <TopCard :stats="ato"/>
  
    <h1 class="text-2xl font-bold mb-4">Pending Application Lists</h1>
    
    <ApplicationTable
      :applications="props.applications"
      @view="handleView"
      @edit="handleEdit"
      @delete="handleDelete"
    />
   
  </LocatorAppSidebarLayout>
</template>
