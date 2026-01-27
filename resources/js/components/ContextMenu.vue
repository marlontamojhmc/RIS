<template>
  <div
    v-if="visible"
    :style="{ top: `${y}px`, left: `${x}px` }"
    class="fixed z-50 bg-white border rounded shadow-lg w-40"
  >
    <ul class="flex flex-col">
      <li
        v-for="(item, index) in items"
        :key="index"
        @click="item.action(); closeMenu()"
        class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
      >
        {{ item.label }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { ref, defineExpose } from 'vue'

const props = defineProps<{
  items: { label: string; action: () => void }[]
}>()

const visible = ref(false)
const x = ref(0)
const y = ref(0)

// Open menu at mouse position
const openMenu = (event: MouseEvent) => {
  event.preventDefault()
  x.value = event.clientX
  y.value = event.clientY
  visible.value = true
  window.addEventListener('click', closeMenu)
}

// Close menu
const closeMenu = () => {
  visible.value = false
  window.removeEventListener('click', closeMenu)
}

// Expose openMenu to parent
defineExpose({ openMenu })
</script>

<style scoped>
/* Optional: add a small fade-in animation */
</style>
