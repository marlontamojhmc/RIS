<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import locatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { locator } from '@/routes';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head , usePage } from '@inertiajs/vue3';
import { ref } from "vue";
import { Users, FileText, UserPlus } from "lucide-vue-next"; // Lucide icons
import applications from '@/routes/applications';
import ApplicationTable from '@/components/common/ApplicationTable.vue';
import TopCard from '@/components/common/TopCard.vue';
import TimeLine from '@/components/locator/TimeLine.vue';
const page = usePage();
// ✅ Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Locator',
    href: locator.url(),
  },
];

// ❌ Your `defineProps` syntax is invalid.
// ✅ Correct `defineProps` object:
const props = defineProps({
  applications: {
    type: [Array, Object], // Array if not paginated, Object if paginated
    default: () => [],
  },
});

// ✅ Temporary stats object (mock data)
const status = {
  atoCertified: 'ATO Certified',
  activeUsers: 'Users',
  sezadRequests: 'Requests',
  bddUsers: 'Valid',
}



const app = page.props.applications[0] ? page.props.applications[0].status : null


</script>

<template>
  <Head title="Locator Dashboard" />
   
  <locatorAppSidebarLayout :breadcrumbs="breadcrumbs">
   
      <!-- Apply New -->
        
       <TopCard :stats="app"/>
        
    <!---table-->
     <div class="mt-6 overflow-x-auto">
 
  <ApplicationTable
      :applications="props.applications"
      @view="handleView"
      @edit="handleEdit"
      @delete="handleDelete"
    /> 
</div>

<!--end table-->
  </locatorAppSidebarLayout>
</template>
