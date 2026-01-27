<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import {  RabbitIcon, RocketIcon } from 'lucide-vue-next';
import { useToast } from 'vue-toastification';
const toast = useToast();

function showSuccess() {
  toast.success('Saved successfully!');
}
function showError() {
  toast.error('Something went wrong!');
}
</script>
<template>
<AppLayout>
Home Page
<RocketIcon/>
<button @click="showSuccess" class="bg-green-500 text-white px-4 py-2 rounded">
    Success Toast
  </button>
  <button @click="showError" class="bg-red-500 text-white px-4 py-2 rounded">
    Error Toast
  </button>

</AppLayout>
</template>