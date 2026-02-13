<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3'
import FinanceAppSidebarLayout from '@/layouts/Finance/FinanceAppsidebarLayout.vue';
import FinanceApplicationTable from './FinanceComponents/FinanceApplicationTable.vue';
const p = defineProps({
  applications:{
      type:Object,
      required: true
 }
})
const form = useForm({
  form_id:'',
  application_form_id:'',
  form_type:'',
})


const handleView = (application)=>{
     console.log(application)
     form.form_id= application.application.form_id
     form.application_form_id= application.application_form_id
     form.form_type = application.application.form_type
     form.post('/fsd/finance')
 
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
   <pre> {{ p.applications }}</pre>
  </div>
  </FinanceAppSidebarLayout>
</template>
