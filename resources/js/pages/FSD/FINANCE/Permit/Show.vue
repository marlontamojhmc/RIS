<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
const props = defineProps({
  applications: {
    type: Array,
    required: true
  }
})
</script>

<template>
<AppLayout>
  <div>
    Permit Finance Show
    <!-- Example of using props -->
    <ul>
      <li v-for="(app, index) in props.applications" :key="index">
        {{ app }}
      </li>
    </ul>
  </div>
  </AppLayout>
</template>
