<script setup lang="ts">
import ServiceProviderSidebarLayout from '@/layouts/ServiceProvider/ServiceProviderSidebarLayout.vue';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import { locator } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage, router } from '@inertiajs/vue3';
import { ref, computed } from "vue";
import { Check } from "lucide-vue-next";


const page = usePage();

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Locator',
    href: locator.url(),
  },
];

// Props
const props = defineProps({
  serviceProvider: {
    type: Array,
    default: () => [],
  },
});

// Check if data is empty
const isEmpty = computed(() => props.serviceProvider.length === 0);

// Approve vendor
const approveVendor = (item: any) => {
  router.post(`/locator/serviceProviderRequest/${item.id}/approve`, {}, {
    onSuccess: (page) => {
      console.log(page.props.flash.success);
    }
  });
};
</script>

<template>
  <Head title="Locator Dashboard" />

  <AppSidebarLayout :breadcrumbs="breadcrumbs">

    <h1 class="text-2xl font-bold mb-4 text-center">Service Provider Requests</h1>

    <div class="mt-6 overflow-x-auto bg-white rounded-lg shadow">
      <table class="min-w-full text-sm border">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 border">ID</th>
            <th class="px-3 py-2 border">Email</th>
            <th class="px-3 py-2 border">Name</th>
            <th class="px-3 py-2 border">Business Type</th>
            <th class="px-3 py-2 border">Status</th>
            <th class="px-3 py-2 border">Action</th>
          </tr>
        </thead>

        <tbody>
          <!-- EMPTY STATE ROW -->
          <tr v-if="isEmpty">
            <td colspan="6" class="text-center py-6 text-gray-500">
              No service provider requests found.
            </td>
          </tr>

          <!-- DATA ROWS -->
          <tr
            v-else
            v-for="(item, index) in props.serviceProvider"
            :key="item.id"
            class="hover:bg-gray-50"
          >
            <td class="px-3 py-2 border">{{ index + 1 }}</td>
            <td class="px-3 py-2 border">{{ item.email }}</td>
            <td class="px-3 py-2 border">{{ item.name }}</td>
            <td class="px-3 py-2 border">{{ item.business_type }}</td>

            <td class="px-3 py-2 border capitalize">{{ item.status }}</td>

            <td class="px-3 py-2 border">
              <button
                @click="approveVendor(item)"
                :disabled="item.status === 'approved'"
                class="flex items-center gap-1 bg-green-600 text-white px-3 py-1 rounded shadow hover:bg-green-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed"
              >
                <Check class="w-4 h-4" />
              </button>
            </td>
          </tr>
        </tbody>

      </table>
    </div>

  </AppSidebarLayout>
</template>
