<script setup lang="ts">
import { onMounted, onUnmounted, watch } from 'vue';

interface Props {
    show: boolean;
    maxWidth?: string;
}

const props = withDefaults(defineProps<Props>(), {
    maxWidth: 'max-w-lg',
});

const emit = defineEmits<{
    (e: 'close'): void;
}>();

/* Lock body scroll */
watch(
    () => props.show,
    (value) => {
        document.body.style.overflow = value ? 'hidden' : '';
    },
);

/* Close on ESC */
const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Escape') {
        emit('close');
    }
};

onMounted(() => {
    window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
    window.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = '';
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
                <!-- Overlay -->
                <div
                    class="fixed inset-0 bg-black/50"
                    @click="emit('close')"
                ></div>

                <!-- Center Wrapper -->
                <div class="flex min-h-screen items-center justify-center p-4">
                    <!-- Modal Box -->
                    <div
                        :class="[
                            'relative z-10 max-h-[90vh] w-full overflow-y-auto rounded-2xl bg-white shadow-xl',
                            maxWidth,
                        ]"
                    >
                        <!-- Header -->
                        <div
                            class="flex items-center justify-between border-b px-4 py-3"
                        >
                            <slot name="header" />
                            <button
                                class="flex h-8 w-8 items-center justify-center rounded-full border text-lg transition hover:bg-gray-100"
                                @click="emit('close')"
                            >
                                ✕
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-4">
                            <slot />
                        </div>

                        <!-- Footer -->
                        <div class="border-t px-4 py-3">
                            <slot name="footer" />
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
