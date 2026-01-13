<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import LocatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { locator } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { Check } from "lucide-vue-next";

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Service Provider', href: locator.url() },
];

// Props
const props = defineProps<{
  serviceProvider: Array<any> | object;
}>();

// Approve vendor
const approveVendor = (item: any) => {
  router.post(`/locator/vendor/${item.id}/approve`, {}, {
    onSuccess: (page) => {
      console.log(page.props.flash?.success);
    }
  });
};
</script>

<template>
  <Head title="Locator Dashboard" />

  <AppSidebarLayout :breadcrumbs="breadcrumbs">
    <h1 class="text-2xl font-bold mb-6 text-center">My Service Provider</h1>

    <div class="mt-6 overflow-x-auto bg-white rounded-lg shadow border">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 border">#</th>
            <th class="px-3 py-2 border">Email</th>
            <th class="px-3 py-2 border">Name</th>
            <th class="px-3 py-2 border">Business Type</th>
            <th class="px-3 py-2 border">Status</th>
          </tr>
        </thead>

        <tbody>
          <!-- No Data -->
          <tr v-if="!props.serviceProvider || props.serviceProvider.length === 0">
            <td colspan="5" class="py-6 text-center text-gray-500">
              No approved service provider found.
            </td>
          </tr>

          <!-- Rows -->
          <tr
            v-for="(item, index) in props.serviceProvider"
            :key="item.id"
            class="hover:bg-gray-50 transition"
          >
            <td class="px-3 py-2 border text-center">{{ index + 1 }}</td>
            <td class="px-3 py-2 border text-center">{{ item.email }}</td>
            <td class="px-3 py-2 border text-center">{{ item.name }}</td>
            <td class="px-3 py-2 border text-center">{{ item.business_type }}</td>
            <td class="px-3 py-2 border text-center capitalize">
              {{ item.status }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </AppSidebarLayout>
</template>
