<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import locatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { locator } from '@/routes';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head , usePage, router } from '@inertiajs/vue3';
import { ref } from "vue";
import { Users, FileText, UserPlus } from "lucide-vue-next"; // Lucide icons
import applications from '@/routes/applications';
import ApplicationTable from '@/components/common/ApplicationTable.vue';
import TopCard from '@/components/common/TopCard.vue';
import TimeLine from '@/components/locator/TimeLine.vue';

const page = usePage();
const loading = ref(false);
// ✅ Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Locator',
    href: locator.url(),
  },
];
function handleEdit(app: any) {
  loading.value = true
  console.log(JSON.stringify(app, null, 2))
  router.visit(`/applications/${app.application_id}/edit`)
  
}
function handleView(app:any){
  loading.value = true
  const matchedApp = props.applications_with_form_type.find(
  (a) => a.id === app.application_id
)

const formType = matchedApp ? matchedApp.form_type : null
if(formType =='ATO'){
router.visit(`/ATO/${app.application_id}`)
}else{
 router.visit(`/applications/${app.application_id}/edit`)
  
}
}

const props = defineProps({
  applications: {
    type: [Array, Object], // Array if not paginated, Object if paginated
    default: () => [],
  },
  applications_with_form_type:{
    type:[Array, Object],
    default: () =>[],
  }
});
const matchedApp = props.applications_with_form_type.find(
  (a) => a.id === props.applications.application_id
)

const formType = matchedApp ? matchedApp.form_type : null

console.log("Form Type:", formType)

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
   
    <!--Loading-->
    <div
  v-if="loading"
  class="fixed inset-0 z-50 flex items-center justify-center bg-background/80 backdrop-blur-sm"
>
  <div class="flex flex-col items-center gap-3">
    <svg
      class="h-8 w-8 animate-spin text-primary"
      viewBox="0 0 24 24"
    >
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
        fill="none"
      />
      <path
        class="opacity-75"
        fill="currentColor"
        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
      />
    </svg>

    <p class="text-sm text-muted-foreground">
      Loading Data, please wait…
    </p>
  </div>
</div>
    <!--end loading-->
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
