<script setup lang="ts">
import { ref } from 'vue'
import type { NavItem } from '@/types'
import { Link } from '@inertiajs/vue3'

defineProps<{
  items: NavItem[]
}>()

// track which dropdown is open
const openDropdown = ref<string | null>(null)
const toggleDropdown = (title: string) => {
  openDropdown.value = openDropdown.value === title ? null : title
}
</script>

<template>
  <nav class="space-y-1">
    <template v-for="item in items" :key="item.title">
      <div>
        <!-- Parent with dropdown -->
        <div v-if="item.children">
          <button
            @click="toggleDropdown(item.title)"
            class="w-full flex items-center justify-between px-3 py-2 rounded-md
                   text-gray-800 hover:bg-gray-100
                   dark:text-gray-200 dark:hover:bg-gray-800
                   transition"
          >
            <div class="flex items-center gap-2">
              <component v-if="item.icon" :is="item.icon" class="w-4 h-4" />
              <span>{{ item.title }}</span>
            </div>
            <svg
              class="w-4 h-4 transform transition-transform duration-200
                     text-gray-600 dark:text-gray-400"
              :class="{ 'rotate-90': openDropdown === item.title }"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
          </button>

          <!-- Dropdown children -->
          <transition name="fade">
            <div
              v-if="openDropdown === item.title"
              class="ml-6 mt-1 space-y-1 border-l pl-3 text-sm
                     border-gray-200 dark:border-gray-700
                     text-gray-700 dark:text-gray-300"
            >
              <Link
                v-for="child in item.children"
                :key="child.title"
                :href="child.href"
                class="block px-2 py-1 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50"
              >
                {{ child.title }}
              </Link>
            </div>
          </transition>
        </div>

        <!-- Single link (no children) -->
        <Link
          v-else
          :href="item.href"
          class="flex items-center gap-2 px-3 py-2 rounded-md
                 text-gray-800 hover:bg-gray-100
                 dark:text-gray-200 dark:hover:bg-gray-800
                 transition"
        >
          <component v-if="item.icon" :is="item.icon" class="w-4 h-4" />
          <span>{{ item.title }}</span>
        </Link>
      </div>
    </template>
  </nav>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}
</style>
