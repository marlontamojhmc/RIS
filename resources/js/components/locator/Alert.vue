<script setup lang="ts">
import { ref, watch } from 'vue'

// Props
const props = defineProps({
  message: { type: String, required: true },
  type: { type: String as () => 'success' | 'error' | 'info', default: 'info' },
  duration: { type: Number, default: 0 } // optional auto-dismiss in ms, 0 = no auto-dismiss
})

const visible = ref(true)

const close = () => {
  visible.value = false
}

// Optional: auto-dismiss
if (props.duration > 0) {
  setTimeout(() => {
    visible.value = false
  }, props.duration)
}
</script>

<template>
  <div v-if="visible"
       :class="[
         'px-4 py-2 rounded-md flex justify-between items-center mb-4',
         type === 'success' ? 'bg-green-100 text-green-800' :
         type === 'error' ? 'bg-red-100 text-red-800' :
         'bg-blue-100 text-blue-800'
       ]">
    <span>{{ message }}</span>
    <button @click="close" class="ml-4 font-bold text-lg leading-none">✕</button>
  </div>
</template>
