<script setup>
import { router } from '@inertiajs/vue3'
import OsacAppSidebarLayout from '@/layouts/Osac/OsacAppSidebarLayout.vue';
import osacApplicationTable from './OsacComponents/osacApplicationTable.vue';
const p = defineProps({
  applications:{
      type:Object,
      required: true
 }
})
const handleView = (application)=>{
  console.log('osac Viewing:',application.application_form_id)
  router.visit(`/sezad/osac/${application.application_form_id}/show`)
}
const handleEdit = (application) =>{
  console.log(application)
}
const handleDelete =(application) =>{
  console.log(application)
}
</script>

<template>
    <OsacAppSidebarLayout>
  <div>
    <h1 class="text-2xl font-bold text-center mb-4">OSAC Dashboard</h1>
    
    <osacApplicationTable 
     :applications="p.applications"
      @view="handleView"
      @edit="handleEdit"
      @delete="handleDelete"
    />
    
  </div>
  </OsacAppSidebarLayout>
</template>
