<script setup lang="ts">
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';

interface BusinessType {
  id: number;
  name: string;
}

interface TempUser {
  id: number;
  email: string;
  name: string;
  locator: string;
  status: string;
  business_type: number;
  businessType?: BusinessType;
}

const props = defineProps<{
  spsnbe: TempUser;
}>();
</script>

<template>
  <AppSidebarLayout>
    

    <h1 class="text-2xl text-center font-bold text-gray-900 mb-4">
      Service Provider
    </h1>

    <!-- Display actual fields -->
   <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
  <tbody class="divide-y divide-gray-200">

    <tr>
      <th class="bg-gray-100 px-4 py-2 text-left font-semibold w-40">Business Name</th>
      <td class="px-4 py-2">{{ props.spsnbe.name }}</td>
    </tr>

    <tr>
      <th class="bg-gray-100 px-4 py-2 text-left font-semibold">Email</th>
      <td class="px-4 py-2">{{ props.spsnbe.email }}</td>
    </tr>

    <tr>
      <th class="bg-gray-100 px-4 py-2 text-left font-semibold">Locator</th>
      <td class="px-4 py-2">{{ props.spsnbe.locator }}</td>
    </tr>

    <tr>
      <th class="bg-gray-100 px-4 py-2 text-left font-semibold">Status</th>
      <td class="px-4 py-2">{{ props.spsnbe.status }}</td>
    </tr>

    <tr>
      <th class="bg-gray-100 px-4 py-2 text-left font-semibold">Business Type</th>
      <td class="px-4 py-2">{{ props.spsnbe.business_type.name }}</td>
    </tr>
    <tr>
      <th class="bg-gray-100 px-4 py-2 text-left font-semibold">Business Type Description</th>
      <td class="px-4 py-2">{{ props.spsnbe.business_type.description }}</td>
    </tr>

  </tbody>
</table>
  </AppSidebarLayout>
</template>
