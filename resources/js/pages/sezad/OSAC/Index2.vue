<script setup>
import Button from '@/components/ui/button/Button.vue';
import ApplicationModal from '@/components/View/ApplicationModal.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    application: {
        type: Object, // ✅ FIXED
        required: true,
    },
});

const showModal = ref(false);

const form = useForm({
    id: null,
});

const HandlePass = () => {
    form.id = props.application.id; // ✅ use real id
    form.post('/sezad/store2', {
        onSuccess: () => {
            console.log('Sent successfully');
        },
    });
};
</script>

<template>
    <AppLayout>
        <div>Index2 vue Index2 method osac</div>

        <Button @click="showModal = true"> View Application </Button>

        <Button @click="HandlePass"> test me </Button>

        <ApplicationModal
            :show="showModal"
            :application="props.application"
            @close="showModal = false"
        />
    </AppLayout>
</template>
