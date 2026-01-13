<script setup lang="ts">
import { ref, onMounted } from 'vue';

const messages = ref<string[]>([]);

onMounted(async () => {
  // Step 1: fetch token request from your Laravel backend
  const tokenResp = await fetch('/ably/token-request');
  const tokenRequest = await tokenResp.json();

  // Step 2: initialize Ably Realtime client with token auth
  const realtime = new (window as any).Ably.Realtime({ token: tokenRequest });

  // Step 3: get channel
  const channel = realtime.channels.get('get-started');

  // Step 4: subscribe to messages named "first"
  channel.subscribe('first', (msg: any) => {
    messages.value.push(msg.data);
  });
});
</script>

<template>
  <div class="p-4">
    <h2 class="text-xl font-bold mb-2">Ably Messages</h2>
    <ul class="list-disc pl-5">
      <li v-for="(msg, index) in messages" :key="index">{{ msg }}</li>
    </ul>
  </div>
</template>

<style scoped>
/* Optional styling */
ul {
  max-height: 300px;
  overflow-y: auto;
}
</style>
