<template>
  <div class="space-y-4">
    <!-- Category -->
    <div>
      <label class="block font-medium text-gray-700 mb-1 dark:text-gray-200">
        Category
      </label>
      <select
        v-model="selectedCategory"
        class="w-full border border-gray-300 p-2 rounded
               bg-white text-gray-900
               dark:bg-gray-800 dark:border-gray-600 dark:text-gray-100
               focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-500"
      >
        <option value="" class="dark:bg-gray-800 dark:text-gray-400">
          -- Select Category --
        </option>
        <option
          v-for="cat in categories"
          :key="cat.id"
          :value="cat.id"
          class="dark:bg-gray-800 dark:text-gray-100"
        >
          {{ cat.name }}
        </option>
      </select>
    </div>

    <!-- Options -->
    <div v-if="options.length">
      <label class="block font-medium text-gray-700 mb-1 dark:text-gray-200">
        Options / Validity
      </label>
      <select
        v-model="selectedOption"
        class="w-full border border-gray-300 p-2 rounded
               bg-white text-gray-900
               dark:bg-gray-800 dark:border-gray-600 dark:text-gray-100
               focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-500"
      >
        <option value="" class="dark:bg-gray-800 dark:text-gray-400">
          -- Select Option --
        </option>
        <option
          v-for="opt in options"
          :key="opt.id"
          :value="opt.id"
          class="dark:bg-gray-800 dark:text-gray-100"
        >
          {{ opt.validity.label }}
        </option>
      </select>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, defineProps, defineEmits } from 'vue'

const props = defineProps({
  categories: { type: Array, required: true },
  modelValue: { type: [String, Number], default: '' }
})
const emit = defineEmits(['update:modelValue'])

const selectedCategory = ref('')
const options = ref<any[]>([])
const selectedOption = ref('')

// Update options when category changes
watch(selectedCategory, (catId) => {
  const cat = props.categories.find(c => c.id == catId)
  options.value = cat ? cat.options : []
  selectedOption.value = ''
  emit('update:modelValue', '')
})

// Emit when option changes
watch(selectedOption, (val) => emit('update:modelValue', val))
</script>
