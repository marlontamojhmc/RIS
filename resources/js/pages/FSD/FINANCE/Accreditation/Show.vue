<script setup>
import { defineProps } from 'vue'
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
  application: {type: Object,required: true},
  accreditation:{type: Object,required:true},
})
</script>
<template>
<AppLayout>
Accreditation Show
{{ props.accreditation }}
</AppLayout>
</template>