<script setup lang="ts">
import AppContent from '@/components/AppContent.vue'
import AppShell from '@/components/AppShell.vue'
import CcoAppsidebar from '@/components/Cco/Cco-Appsidebar.vue'
//import OsacAppsidebar from '@/components/OsacComponents/Osac-Appsidebar.vue'
import AppSidebarHeader from '@/components/AppSidebarHeader.vue'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  breadcrumbs?: BreadcrumbItem[]
}>()
</script>

<template>
  <AppShell variant="sidebar">
    <CcoAppsidebar :breadcrumbs="props.breadcrumbs ?? []" />
    <AppContent variant="sidebar" class="overflow-x-hidden">
      <AppSidebarHeader :breadcrumbs="props.breadcrumbs ?? []" />
      <slot />
    </AppContent>
  </AppShell>
</template>
