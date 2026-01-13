<script setup lang="ts">
import { dashboard, login, register ,signup} from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
  <Head title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
  </Head>

  <div
    class="flex min-h-screen flex-col items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a]"
  >
    <!-- Card -->
    <div
      class="flex w-full max-w-sm flex-col items-center rounded-2xl bg-white p-8 shadow-lg dark:bg-[#1b1b18] transition-all"
    >
      <!-- Logo -->
      <img
        src="storage/images/jhmc_logo.png"
        alt="Logo"
        class="mb-4 w-48 h-48 object-contain"
      />

      <!-- Heading -->
      <h2 class="mb-6 text-2xl font-semibold text-center">
        Welcome to SEZRIS
      </h2>

      <!-- Navigation (login button or dashboard) -->
      <nav class="flex flex-col items-center gap-4 w-full">
        <Link
          v-if="$page.props.auth.user"
          :href="login()"
          class="w-full text-center rounded-lg border border-[#19140035] px-5 py-2 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
        >
          Login
        </Link>
        <template v-else>
          <Link
            :href="login()"
            class="w-full text-center rounded-lg bg-[#0F75BC] px-5 py-2 text-sm font-medium text-white hover:bg-[#0c5f96] transition"
          >
            Log in
          </Link>
          <!-- Uncomment if you want register button too
          <Link
          <!-- Uncomment if you want register button too -->
          <!-- <Link
            :href="register()"
            class="w-full text-center rounded-lg border border-[#19140035] px-5 py-2 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
          >
            Register
          </Link> -->
          <Link
            :href="signup()"
            class="w-full text-center rounded-lg border border-[#19140035] px-5 py-2 text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
          >
           Sign up
          </Link> 
        </template>
      </nav>
    </div>
  </div>
</template>
