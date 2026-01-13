<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- ✅ Sidebar (desktop) -->
    <aside
      class="hidden md:flex md:w-64 md:flex-col bg-white shadow-lg border-r border-gray-200"
    >
      <div class="h-16 flex items-center justify-center border-b">
        <h1 class="text-xl font-bold text-gray-700">My Dashboard</h1>
      </div>

      <nav class="flex-1 p-4 space-y-2">
        <Link
          href="/osac"
          class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100"
        >
          OSAC
        </Link>
        <Link
          href="/locator"
          class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100"
        >
          Locator
        </Link>
      </nav>
    </aside>

    <!-- ✅ Mobile Sidebar Overlay -->
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog as="div" class="relative z-40 md:hidden" @close="sidebarOpen = false">
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-gray-600 bg-opacity-75" />
        </TransitionChild>

        <div class="fixed inset-0 flex z-40">
          <TransitionChild
            as="template"
            enter="transition ease-in-out duration-300 transform"
            enter-from="-translate-x-full"
            enter-to="translate-x-0"
            leave="transition ease-in-out duration-300 transform"
            leave-from="translate-x-0"
            leave-to="-translate-x-full"
          >
            <DialogPanel
              class="relative flex w-full max-w-xs flex-col bg-white shadow-xl"
            >
              <div class="h-16 flex items-center justify-between px-4 border-b">
                <h1 class="text-lg font-bold">Menu</h1>
                <button @click="sidebarOpen = false" class="text-gray-500">
                  ✕
                </button>
              </div>

              <nav class="flex-1 p-4 space-y-2">
                <Link
                  href="/osac"
                  class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100"
                  @click="sidebarOpen = false"
                >
                  OSAC
                </Link>
                <Link
                  href="/locator"
                  class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-100"
                  @click="sidebarOpen = false"
                >
                  Locator
                </Link>
              </nav>
            </DialogPanel>
          </TransitionChild>
          <div class="w-14 flex-shrink-0" aria-hidden="true"></div>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- ✅ Main content -->
    <div class="flex-1 flex flex-col">
      <!-- Header -->
      <header
        class="sticky top-0 z-10 bg-white border-b h-16 flex items-center justify-between px-4 shadow-sm"
      >
        <button
          class="md:hidden text-gray-600 focus:outline-none"
          @click="sidebarOpen = true"
        >
          ☰
        </button>
        <h2 class="text-lg font-semibold text-gray-700">{{ title }}</h2>
      </header>

      <!-- Page content -->
      <main class="flex-1 p-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import {
  Dialog,
  DialogPanel,
  TransitionChild,
  TransitionRoot
} from '@headlessui/vue'

const sidebarOpen = ref(false)
defineProps({
  title: { type: String, default: 'Dashboard' }
})
</script>
