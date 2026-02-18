<script setup lang="ts">
interface Props {
    show: boolean;
    maxWidth?: string;
}

withDefaults(defineProps<Props>(), {
    maxWidth: 'max-w-lg',
});

const emit = defineEmits<{
    (e: 'close'): void;
}>();
</script>

<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center"
        >
            <!-- Overlay -->
            <div
                class="absolute inset-0 bg-black/50"
                @click="emit('close')"
            ></div>

            <!-- Modal Container -->
            <div
                :class="[
                    'relative z-10 w-full rounded-2xl bg-white p-3 shadow-xl',
                    maxWidth,
                ]"
            >
                <!-- Header -->
                <div class="mb-1 flex items-center justify-end">
                    <slot name="header" />
                    <button
                        class="flex h-8 w-8 items-center justify-center rounded-full border text-lg transition hover:bg-gray-100"
                        @click="emit('close')"
                    >
                        ✕
                    </button>
                </div>

                <!-- Body -->
                <div class="mb-2">
                    <slot />
                </div>

                <!-- Footer -->
                <div>
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Teleport>
</template>
