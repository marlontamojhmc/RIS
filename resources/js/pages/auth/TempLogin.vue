<script setup lang="ts">
import { reactive, ref } from "vue";
import { Head, router } from "@inertiajs/vue3";

const form = reactive({
  email: "",
  password: "",
});

const loading = ref(false);
const errorMessage = ref("");

const submit = () => {
  loading.value = true;
  errorMessage.value = "";

  router.post("/temp/login", form, {
    onError: (errors) => {
      loading.value = false;
      errorMessage.value = errors.message ?? "Invalid credentials.";
    },
    onSuccess: () => {
      loading.value = false;
    }
  });
};
</script>

<template>
  <Head title="Temporary Login" />

  <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
    <div class="bg-white shadow rounded-lg p-8 w-full max-w-md">

      <h2 class="text-2xl font-bold text-center mb-6">Temporary User Login</h2>

      <!-- Error -->
      <div
        v-if="errorMessage"
        class="mb-4 p-3 rounded bg-red-100 text-red-700 text-sm"
      >
        {{ errorMessage }}
      </div>

      <!-- Form -->
      <form @submit.prevent="submit" class="space-y-4">

        <div>
          <label class="block text-sm font-medium mb-1">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="w-full border rounded px-3 py-2"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Password</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="w-full border rounded px-3 py-2"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition disabled:bg-gray-400"
        >
          <span v-if="loading">Logging in...</span>
          <span v-else>Login</span>
        </button>
      </form>
    </div>
  </div>
</template>
