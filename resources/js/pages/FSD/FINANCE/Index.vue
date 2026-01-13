<script setup>
import { router } from '@inertiajs/vue3'
import FinanceAppSidebarLayout from '@/layouts/Finance/FinanceAppsidebarLayout.vue';
import FinanceApplicationTable from './FinanceComponents/FinanceApplicationTable.vue';
const p = defineProps({
  applications:{
      type:Object,
      required: true
 }
})
const handleView = (application)=>{
  console.log('osac Viewing:',application.application_form_id)
  router.visit(`/fsd/finance/${application.application_form_id}/show`)
}
const handleEdit = (application) =>{
  console.log(application)
}
const handleDelete =(application) =>{
  console.log(application)
}
</script>

<template>
    <FinanceAppSidebarLayout>
  <div>
    <h1 class="text-2xl font-bold text-center mb-4">Finance Dashboard</h1>
    
    <FinanceApplicationTable
     :applications="p.applications"
      @view="handleView"
      @edit="handleEdit"
      @delete="handleDelete"
    />
    
  </div>
    </FinanceAppSidebarLayout>
</template>
