<template>
  <div class="flex items-center justify-center min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8 relative">

    <!-- ===== Loading Overlay ===== -->
    <div v-if="loading" class="absolute inset-0 bg-white/30 bg-opacity-40 flex items-center justify-center z-50"
      style="backdrop-filter: blur(2px);">
      <div class="flex flex-col items-center">
        <!-- BLACK SPINNER -->
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-black border-t-transparent"></div>

        <!-- BLACK TEXT -->
        <p class="text-black mt-3 text-lg">Please wait...</p>
      </div>
    </div>
    <!-- =========================== -->

    <div class="bg-white shadow-xl rounded-2xl w-full max-w-md p-6 sm:p-8">
      <h2 class="text-2xl sm:text-3xl font-bold text-center mb-6" style="color: #0f75bc;">
        Business Registration
      </h2>

      <form @submit.prevent="handleSubmit" class="space-y-5">

        <!-- Email -->
        <div>
          <label class="block text-gray-600 text-sm mb-1">Email</label>
          <input v-model="form.email" type="email"
            class="w-full border rounded-lg px-4 py-2 sm:py-3 focus:outline-none focus:ring-2 focus:ring-[#0f75bc]"
            placeholder="Enter your email" required />
        </div>

        <!-- Business Name -->
        <div>
          <label class="block text-gray-600 text-sm mb-1">Business Name</label>
          <input v-model="form.businessName" type="text"
            class="w-full border rounded-lg px-4 py-2 sm:py-3 focus:outline-none focus:ring-2 focus:ring-[#0f75bc]"
            placeholder="Enter business name" required />
        </div>

        <!-- Business Type -->
        <div>
          <label class="block text-gray-600 text-sm mb-1">Select Business Type</label>
          <select v-model="selectedBusinessType"
            class="w-full border rounded-lg px-4 py-2 sm:py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#0f75bc]">
            <option value="">-- Choose Business Type --</option>
            <option v-for="type in businessTypes" :key="type.id" :value="type.id">
              {{ type.description }}
            </option>
          </select>
        </div>

        <!-- Locator Dropdown -->
        <div>
          <label class="block text-gray-600 text-sm mb-1">Select Locator</label>
          <select v-model="selectedLocator" @change="addLocator"
            class="w-full border rounded-lg px-4 py-2 sm:py-3 bg-white focus:outline-none focus:ring-2 focus:ring-[#3ab54a]">
            <option value="">-- Choose a Locator --</option>
            <option v-for="locator in locators" :key="locator.id" :value="locator.email">
              {{ locator.name }}
            </option>
          </select>

          <div class="flex flex-wrap gap-2 mt-3">
            <div v-for="(item, index) in form.selectedLocators" :key="index"
              class="flex items-center gap-2 bg-[#e8f6ef] border border-[#3ab54a] text-[#0f75bc] px-3 py-1 rounded-full text-sm sm:text-base">
              <span>{{ item }}</span>
              <button type="button" @click="removeLocator(index)"
                class="text-white bg-[#3ab54a] hover:bg-[#0f75bc] rounded-full w-5 h-5 flex items-center justify-center font-bold text-xs">
                ×
              </button>
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-col sm:flex-row justify-between gap-3 mt-6">
          <button type="button"
            class="w-full sm:w-auto px-6 py-2 sm:py-3 rounded-lg border border-[#0f75bc] text-[#0f75bc] hover:bg-[#0f75bc] hover:text-white transition font-medium"
            @click="resetForm" :disabled="loading">
            Cancel
          </button>

          <button type="submit"
            class="w-full sm:w-auto px-6 py-2 sm:py-3 rounded-lg bg-[#3ab54a] text-white hover:bg-[#0f75bc] transition font-medium flex items-center justify-center gap-2"
            :disabled="loading">

            <span v-if="!loading">Sign Up</span>
            <span v-else class="flex items-center gap-2">
              <div class="animate-spin rounded-full h-4 w-4 border-2 border-white border-t-transparent"></div>
              Processing...
            </span>
          </button>
        </div>
      </form>

      <div class="mt-6 text-center sm:text-right text-sm">
        <span class="text-gray-600">Already signed up?</span>
        <a href="/login" class="font-semibold ml-1" style="color: #0f75bc;">Login here</a>
      </div>
    </div>

    <!-- Toast -->
    <div v-if="toast.show"
      class="fixed top-6 right-6 z-50 px-4 py-3 rounded-lg shadow-lg text-white transition-all duration-500"
      :class="toast.type === 'success' ? 'bg-green-600' : 'bg-red-600'">
      {{ toast.message }}
    </div>

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import axios from "axios";

// Loading state
const loading = ref(false);

type BusinessType = { id: number; description: string };
type Locator = { id: number; name: string; email: string };

// FORM DATA
const form = ref({
  email: "",
  businessName: "",
  businessType: "",
  selectedLocators: [] as string[],
});

// Toast
const toast = ref({
  show: false,
  message: "",
  type: "success" as "success" | "error",
});

function showToast(message: string, type: "success" | "error" = "success") {
  toast.value = { show: true, message, type };
  setTimeout(() => (toast.value.show = false), 5000);
}

// Dropdown data
const businessTypes = ref<BusinessType[]>([]);
const locators = ref<Locator[]>([]);

const selectedBusinessType = ref("");
const selectedLocator = ref("");

// Fetch Data
const fetchBusinessTypes = async () => {
  try {
    const res = await axios.get("/business-types");
    businessTypes.value = res.data;
  } catch { }
};

const fetchLocators = async () => {
  try {
    const res = await axios.get("/locatorsSignUp");
    locators.value = res.data;
  } catch { }
};

onMounted(() => {
  fetchBusinessTypes();
  fetchLocators();
});

// Add/remove locators
const addLocator = () => {
  if (selectedLocator.value && !form.value.selectedLocators.includes(selectedLocator.value)) {
    form.value.selectedLocators.push(selectedLocator.value);
  }
  selectedLocator.value = "";
};

const removeLocator = (index: number) => {
  form.value.selectedLocators.splice(index, 1);
};

// Reset form
const resetForm = () => {
  form.value = { email: "", businessName: "", businessType: "", selectedLocators: [] };
  selectedBusinessType.value = "";
  selectedLocator.value = "";
};

// Submit
const handleSubmit = async () => {
  form.value.businessType = selectedBusinessType.value;

  loading.value = true; // disable UI + show overlay

  try {
    const response = await axios.post("/signupSave", form.value);

    showToast("Registration successful!", "success");
    resetForm();

  } catch (error: any) {
    let msg = "Something went wrong. Please try again.";
    if (error.response?.data?.message) msg = error.response.data.message;

    showToast(msg, "error");
  }

  loading.value = false;
};
</script>
