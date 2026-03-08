<script setup lang="ts">
import { login, signup } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';
import { Check } from 'lucide-vue-next';
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
            class="flex w-full max-w-sm flex-col items-center rounded-2xl bg-white p-8 shadow-lg transition-all dark:bg-[#1b1b18]"
        >
            <!-- Logo -->
            <!-- <img
        src=""
        alt="Logo"
        class="mb-4 w-48 h-48 object-contain"
      /> -->

            <!-- Heading -->
            <h2
                class="mb-6 text-center text-2xl font-semibold text-black dark:text-gray-400"
            >
                RIS
            </h2>

            <!-- Navigation (login button or dashboard) -->
            <nav class="flex w-full flex-col items-center gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    :href="login()"
                    class="w-full rounded-lg border border-[#19140035] px-5 py-2 text-center text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                >
                    <Check class="h-4 w-4 text-green-600" /> Re-Activate User
                    Dashboard
                </Link>
                <template v-else>
                    <Link
                        :href="login()"
                        class="w-full rounded-lg bg-[#0F75BC] px-5 py-2 text-center text-sm font-medium text-white transition hover:bg-[#0c5f96]"
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
                        class="w-full rounded-lg border border-[#19140035] px-5 py-2 text-center text-sm font-medium text-[#1b1b18] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:text-[#EDEDEC] dark:hover:border-[#62605b]"
                    >
                        Sign up
                    </Link>
                </template>
            </nav>
        </div>
    </div>
</template>
