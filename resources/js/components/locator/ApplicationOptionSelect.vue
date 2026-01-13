<script setup lang="ts">
import { computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

const props = defineProps({
  modelValue: [String, Number],
  options: { type: Array, default: () => [] },
  applicationId: [String, Number],
})

const emit = defineEmits(['update:modelValue', 'price-updated'])

const selectedOption = computed(() => {
  return props.options.find(opt => Number(opt.id) === Number(props.modelValue)) || null
})

const handleSelect = (option: any) => {
  emit('update:modelValue', option.id)

  const form = useForm({
    application_id: props.applicationId,
    option_id: option.id,
  })

  form.post('/loctr/applications/option-selection', {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Option saved successfully')
      const updatedPrice = option.price ?? option.value ?? null
      if (updatedPrice !== null) {
        emit('price-updated', updatedPrice)
      }
    },
    onError: (errors) => {
      console.error('❌ Failed to save option selection:', errors)
    },
  })
}
</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <button
        type="button"
        class="w-[4/12] text-left px-3 py-1 bg-gray-100 border border-gray-400 rounded
               focus:outline-none focus:ring-1 focus:ring-gray-300
               dark:bg-gray-800 dark:border-gray-600 dark:text-gray-100 dark:focus:ring-gray-500"
      >
        <span v-if="selectedOption">{{ selectedOption.name }}</span>
        <span v-else class="text-gray-600 italic dark:text-gray-400">-- Select Declared Value --</span>
      </button>
    </DropdownMenuTrigger>

    <DropdownMenuContent
      align="start"
      class="w-[var(--radix-dropdown-menu-trigger-width)]
             bg-white shadow-md rounded-md border border-gray-200/50
             dark:bg-gray-800 dark:border-gray-700 dark:shadow-black/40"
    >
      <DropdownMenuItem
        v-for="option in props.options"
        :key="option?.id"
        @click="handleSelect(option)"
        class="hover:bg-gray-100 cursor-pointer px-3 py-2 transition-colors
               dark:hover:bg-gray-700 dark:text-gray-100"
      >
        {{ option?.name }} - {{ option?.validity }}
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

<style scoped>
button:focus {
  outline: none;
  box-shadow: none;
}
</style>
