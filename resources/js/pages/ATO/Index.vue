<script setup>
import { useForm } from '@inertiajs/vue3';
import LocatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue';
import AppLayout from '@/layouts/AppLayout.vue';
const props = defineProps({
  ato: String
})
</script>
<template>
  <AppLayout>
   
    <div>
      <strong>Success</strong><p>your ATO is Valid</p>
    </div>
    <div >
      <p> ATO Application Still waiting for Approval from Approvers</p>
    </div>
  </AppLayout>
    
  
   </template>