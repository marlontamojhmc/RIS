<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-900 px-6 py-12">
    <div class="text-center max-w-md text-gray-100">
      <!-- Optional: Stylish Error Icon -->
      <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto h-20 w-20 text-red-500 mb-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14v.01M12 10h.01M4.75 4.75l14.5 14.5M4.75 19.25l14.5-14.5" />
      </svg>

      <h1 class="text-6xl font-extrabold text-white">404</h1>
      <p class="mt-4 text-2xl font-semibold text-gray-200">Page Not Found</p>
      <p class="mt-2 text-gray-400">
        Sorry, the page you are looking for doesn’t exist or has been moved.
      </p>

      <div class="mt-6">
        <Link
          href="/dashboard"
          class="inline-block bg-red-600 text-white px-6 py-3 rounded-md text-sm font-medium hover:bg-red-700 transition"
        >
          ← Back to dashboard
        </Link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
