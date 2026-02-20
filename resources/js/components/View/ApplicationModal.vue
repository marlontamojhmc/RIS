<script setup>
import { defineEmits, defineProps } from 'vue';

const props = defineProps({
    show: Boolean,
    application: Object,
});

const emit = defineEmits(['close']);
</script>

<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="w-full max-w-lg rounded-xl bg-white p-6 shadow-xl">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Application Details</h2>
                <button
                    @click="emit('close')"
                    class="text-gray-500 hover:text-black"
                >
                    ✕
                </button>
            </div>

            <div class="space-y-2 text-sm">
                <p><strong>ID:</strong> {{ application.id }}</p>
                <p><strong>Form Type:</strong> {{ application.form_type }}</p>
                <p><strong>Title:</strong> {{ application.form_title }}</p>
                <p><strong>Status:</strong> {{ application.status }}</p>
                <p><strong>Price:</strong> {{ application.price ?? 'N/A' }}</p>
                <p><strong>User ID:</strong> {{ application.user_id }}</p>
                <p><strong>Form ID:</strong> {{ application.form_id }}</p>
                <p>
                    <strong>Control Number:</strong>
                    {{ application.control_number ?? 'N/A' }}
                </p>
                <p>
                    <strong>Form Number:</strong> {{ application.form_number }}
                </p>
                <p><strong>Created At:</strong> {{ application.created_at }}</p>
                <p><strong>Updated At:</strong> {{ application.updated_at }}</p>
            </div>

            <div class="mt-6 text-right">
                <button
                    @click="emit('close')"
                    class="rounded-lg bg-black px-4 py-2 text-white"
                >
                    Close
                </button>
            </div>
        </div>
    </div>
</template>
