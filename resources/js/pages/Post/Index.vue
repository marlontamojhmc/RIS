<script setup>
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { nextTick, onMounted, ref } from 'vue';
import { useToast } from 'vue-toastification';
const toast = useToast();
const connected = ref(false);
const messages = ref([]);
const newMessage = ref('');
const chatContainer = ref(null); // for auto-scroll

// Listen to Reverb events
onMounted(() => {
    if (!window.Echo) {
        console.error('❌ Echo not initialized');
        return;
    }

    window.Echo.channel('app-notifications')
        .subscribed(() => {
            connected.value = true;
            toast.success('✅ Connected to app-notification');
            console.log('✅ Connected to app-notification');
        })
        .listen('ApplicationUpdateEvent', (e) => {
            console.log('Received event:', e);

            // Safe access for Reverb and Pusher
            const msg = e.message ?? e.data?.message ?? 'No message';
            messages.value.push(msg);

            newMessage.value = '';

            // Auto-scroll to bottom
            nextTick(() => {
                if (chatContainer.value) {
                    chatContainer.value.scrollTop =
                        chatContainer.value.scrollHeight;
                }
            });
        });
});

// Trigger event

function triggerEvent() {
    fetch(`/event`)
        .then((res) => res.text())
        .then((data) => {
            toast.success(data);
            console.log('Server response:', data);
        })
        .catch((err) => console.error(err));
}
</script>
<template>
    <AppLayout
        >{{ messages }}Index Reverb
        <Button @click="triggerEvent">push</Button>
    </AppLayout>
</template>
