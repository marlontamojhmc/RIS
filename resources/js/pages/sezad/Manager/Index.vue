<script setup>
import { router } from '@inertiajs/vue3'
import SezadManagerAppSidebarLayout from '@/layouts/SezadManager/SezadManagerAppSidebarLayout.vue';
import SezadManagerApplicationTable from './ManagerComponents/SezadManagerApplicationTable.vue';
const p = defineProps({
  applications:{
      type:Object,
      required: true
 }
})
const handleView = (application)=>{
  console.log('osac Viewing:',application.application_form_id)
  router.visit(`/sezad/manager/${application.application_form_id}/show`)
}
const handleEdit = (application) =>{
  console.log(application)
}
const handleDelete =(application) =>{
  console.log(application)
}
</script>

<template>
    <SezadManagerAppSidebarLayout>
  <div>
    <h1 class="text-2xl font-bold text-center mb-4">Sezad Manager Dashboard</h1>
    
    <SezadManagerApplicationTable
     :applications="p.applications"
      @view="handleView"
      @edit="handleEdit"
      @delete="handleDelete"
    />
    
  </div>
  </SezadManagerAppSidebarLayout>
</template>
