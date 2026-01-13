<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';
import { ref } from "vue"
import { Users, FileText, UserPlus } from "lucide-vue-next" // Lucide icons
import users from '@/routes/users';
import type { PageProps } from '@inertiajs/core'
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: dashboard().url,
  },
];



// Example data (later replace with Inertia or API call)

interface UserDetails {
  permission_id: number
  role_id: number
  department_id: number
  division_id: number
  user_function_id: number
}

interface AuthUser {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
  details?: UserDetails
}

interface CustomPageProps extends PageProps {
  auth: {
    user: AuthUser
  }
}

const page = usePage<CustomPageProps>()
const user = page.props.auth.user
// Example data (later replace with Inertia or API call)

</script>

<template>

  <Head title="Dashboard" />

 
    <AppLayout :breadcrumbs="breadcrumbs">
      
     <div class="grid gap-6 md:grid-cols-3 px-6 py-6">
    <!-- Active Users -->
    
    <div
      class="flex flex-col items-center rounded-2xl bg-blue-500 p-6 shadow-lg transition hover:shadow-xl dark:bg-[#1b1b18]"
    >
      <div class="flex items-center gap-3">
        <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900/40">
          <Users class="w-6 h-6 text-[#0F75BC]" />
        </div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
          Active Users
        </h3>
      </div>
      <p class="mt-6 text-4xl font-bold text-black-500">
      
      </p>
    </div>
    </div>


  </AppLayout>
</template>
