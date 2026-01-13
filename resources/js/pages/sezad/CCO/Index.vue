<script setup>
import { router } from '@inertiajs/vue3'
import CcoAppSidebarLayout from '@/layouts/Cco/CcoAppSidebarLayout.vue';
import CcoApplicationTable from './CcoComponents/CcoApplicationTable.vue';
import { usePage } from '@inertiajs/vue3';
const page = usePage();
const p = defineProps({
  applications:{
      type:Object,
      required: true
 }
})
const handleView = (application)=>{
  console.log('Inspector viewing:', application.application_form_id)
 router.visit(`/sezad/cco/${application.application_form_id}/show`)
}
const handleEdit = (application) =>{
  console.log(application)
}
const handleDelete =(application) =>{
  console.log(application)
}
</script>

<template>
    <CcoAppSidebarLayout>
  <div>
    <h1 class="text-2xl font-bold text-center mb-4">CCO/CCA Dashboard </h1>
    <CcoApplicationTable 
     :applications="p.applications"
      @view="handleView"
      @edit="handleEdit"
      @delete="handleDelete"
    />
    
  </div>
  </CcoAppSidebarLayout>
</template>
