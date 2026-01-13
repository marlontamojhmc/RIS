<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { useForm, usePage, Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Dialog, DialogPanel, TransitionRoot } from '@headlessui/vue';
import { Toaster, toast } from 'vue-sonner';
import { type BreadcrumbItem } from '@/types';

/* ---------------------------
   Props / Page
--------------------------- */
const props = defineProps<{
  user: any;
  users: any;
  registered_locators: Array<{
    id: number;
    profile: {
      locator_name: string;
      owner_name: string;
      owner_email: string;
      category?: string;
    };
  }>;
}>();

const page = usePage();

/* ---------------------------
   Types
--------------------------- */
interface Locator {
  id: number;
  locator: string;
  owner: string;
  email: string;
  category: string;
  status: boolean;
}

/* ---------------------------
   Reactive state
--------------------------- */
const search = ref('');
const sortKey = ref<keyof Locator>('locator');
const sortAsc = ref(true);
const currentPage = ref(1);
const perPage = ref(5);

const isAddModalOpen = ref(false);
const isViewModalOpen = ref(false);
const selectedLocator = ref<Locator | null>(null);

/* Build initial locators from props */
const locators = ref<Locator[]>(
  props.registered_locators.map(u => ({
    id: u.id,
    locator: u.profile.locator_name,
    owner: u.profile.owner_name,
    email: u.profile.owner_email,
    category: u.profile.category ?? '',
    status: true,
  }))
);

/* ---------------------------
   Inertia form
--------------------------- */
const form = useForm({
  locator_name: '',
  owner_name: '',
  owner_contact_number: '',
  owner_email: '',
  representative_name: '',
  representative_contact: '',
  representative_email: '',
  official_email_gmail: '',
  applied_date: '',
  applicant_name: '',
  applicant_signature: '',
  type_of_industry: '',
  address_within_jhze: '',
  company_mobile_number: '',
  company_email: '',
  category: '',
  classification: '',
});

/* ---------------------------
   Computed - filtering, sorting, pagination
--------------------------- */
const filtered = computed(() => {
  const q = search.value.trim().toLowerCase();
  const items = locators.value.filter(l => {
    if (!q) return true;
    return Object.values(l).some(v => String(v).toLowerCase().includes(q));
  });

  items.sort((a, b) => {
    const A = String(a[sortKey.value] ?? '').toLowerCase();
    const B = String(b[sortKey.value] ?? '').toLowerCase();
    if (A < B) return sortAsc.value ? -1 : 1;
    if (A > B) return sortAsc.value ? 1 : -1;
    return 0;
  });

  return items;
});

const totalPages = computed(() => Math.max(1, Math.ceil(filtered.value.length / perPage.value)));

const paginated = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filtered.value.slice(start, start + perPage.value);
});

/* ---------------------------
   Helpers & Actions
--------------------------- */
function toggleSort(key: keyof Locator) {
  if (sortKey.value === key) sortAsc.value = !sortAsc.value;
  else {
    sortKey.value = key;
    sortAsc.value = true;
  }
}

function toggleStatus(item: Locator) {
  item.status = !item.status;
}

function openAddModal() {
  form.reset();
  isAddModalOpen.value = true;
}

function openViewModal(item: Locator) {
  selectedLocator.value = item;
  isViewModalOpen.value = true;
}

function closeViewModal() {
  isViewModalOpen.value = false;
  selectedLocator.value = null;
}

/* Client-side required check */
function missingRequiredFields(): string[] {
  const required = ['locator_name', 'owner_name', 'owner_email', 'category'];
  return required.filter(k => !form[k as keyof typeof form].toString().trim());
}

/* Main save handler */
function saveLocator() {
  const missing = missingRequiredFields();
  if (missing.length > 0) {
    // Don't close modal, just show toast
    toast.error('Please complete all fields!', {
      duration: 3000,
      class: 'inline-block text-center py-2 w-full bg-red-400 rounded-sm text-white',
    });
    return; // Stop submission
  }

  const optimistic = {
    id: Date.now(),
    locator: form.locator_name,
    owner: form.owner_name,
    email: form.owner_email,
    category: form.category,
    status: true,
  };

  form.post('/bdd/locator/saveProfile', {
    preserveScroll: true,
    onSuccess: (page) => {
      const msg = page.props.flash?.success ?? 'Locator Registration successfully!';
      toast.success(String(msg), {
        duration: 3000,
        class: 'inline-block max-w-xs bg-blue-600 text-white rounded-sm',
      });
      isAddModalOpen.value = false;
      form.reset();
      locators.value.push(optimistic);
      currentPage.value = totalPages.value;
    },
    onError: (errors) => {
      const first = errors ? Object.values(errors)[0] : null;
      toast.error(String(first ?? 'Validation failed. Please review the form.'), {
        duration: 3000,
        class: 'inline-block max-w-xs bg-red-600 text-white rounded-lg shadow-lg',
      });
    },
  });
}

/* Show flash message on page load if present */
onMounted(() => {
  const msg = page.props.flash?.success;
  if (msg) {
    toast.success(String(msg), {
      duration: 3000,
      class: 'bg-blue-600 text-white rounded-lg shadow-lg',
    });
  }
});

/* Breadcrumbs */
const breadcrumbs: BreadcrumbItem[] = [{ title: 'BDD Dashboard', href: '/' }];
</script>


<template>
  <Head title="BDD Dashboard" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <!-- Toaster -->
    
    <div class="p-6 space-y-6">
      <!-- Search + Add -->
      <div class="flex items-center justify-between mb-4">
        <input
          v-model="search"
          type="text"
          placeholder="Search..."
          class="border rounded-lg px-4 py-2 w-1/3 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        <button
          @click="openAddModal"
          class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700 transition"
        >
          + Add Locator
        </button>
      </div>

      <!-- Table -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-sm">
          <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
            <tr>
              <th @click="toggleSort('locator')" class="cursor-pointer px-4 py-3 text-left">Locator</th>
              <th @click="toggleSort('owner')" class="cursor-pointer px-4 py-3 text-left">Owner</th>
              <th @click="toggleSort('email')" class="cursor-pointer px-4 py-3 text-left">Email</th>
              <th @click="toggleSort('category')" class="cursor-pointer px-4 py-3 text-left">Category</th>
              <th class="px-4 py-3 text-center">Status</th>
              <th class="px-4 py-3 text-center">Actions</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="item in paginated" :key="item.id" class="border-t hover:bg-gray-50 transition">
              <td class="px-4 py-2">{{ item.locator }}</td>
              <td class="px-4 py-2">{{ item.owner }}</td>
              <td class="px-4 py-2">{{ item.email }}</td>
              <td class="px-4 py-2">{{ item.category }}</td>
              <td class="px-4 py-2 text-center">
                <label class="inline-flex items-center cursor-pointer">
                  <input type="checkbox" class="sr-only peer" v-model="item.status" @change="toggleStatus(item)" />
                  <div
                    class="w-10 h-5 bg-gray-300 peer-checked:bg-green-500 rounded-full relative
                      after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-4 after:h-4
                      after:bg-white after:rounded-full after:transition-all peer-checked:after:translate-x-5"
                  />
                </label>
              </td>
              <td class="px-4 py-2 text-center">
                <button
                  @click="openViewModal(item)"
                  class="bg-indigo-600 text-white px-3 py-1 rounded-md shadow hover:bg-indigo-700 transition"
                >
                  View
                </button>
              </td>
            </tr>

            <tr v-if="filtered.length === 0">
              <td colspan="6" class="text-center p-6 text-gray-500">No locators found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-between items-center mt-4">
        <p class="text-sm text-gray-600">Page {{ currentPage }} of {{ totalPages }}</p>

        <div class="space-x-2">
          <button
            @click="currentPage > 1 && currentPage--"
            class="px-4 py-1 border rounded-md bg-white shadow-sm hover:bg-gray-100"
          >
            Prev
          </button>
          <button
            @click="currentPage < totalPages && currentPage++"
            class="px-4 py-1 border rounded-md bg-white shadow-sm hover:bg-gray-100"
          >
            Next
          </button>
        </div>
      </div>
       
      <!-- Add Modal -->
      <TransitionRoot appear :show="isAddModalOpen" as="template">
        <Dialog as="div" class="relative z-10" @close="isAddModalOpen = false">
          <div class="fixed inset-0 bg-black/40" />
          <div class="fixed inset-0 flex items-center justify-center p-4">
            <DialogPanel class="bg-white rounded-xl shadow-2xl w-full max-w-4xl p-6 overflow-y-auto max-h-[90vh]">
              <h2 class="text-2xl font-bold mb-6 text-gray-800">Add New Locator</h2>
               
              <div class="grid grid-cols-2 gap-4 text-sm">
               
                <div>
                  <label class="block text-xs mb-1">Locator name *</label>
                  <input v-model="form.locator_name" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.locator_name" class="text-red-500 text-sm mt-1">{{ form.errors.locator_name }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Owner name *</label>
                  <input v-model="form.owner_name" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.owner_name" class="text-red-500 text-sm mt-1">{{ form.errors.owner_name }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Owner contact *</label>
                  <input v-model="form.owner_contact_number" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.owner_contact_number" class="text-red-500 text-sm mt-1">{{ form.errors.owner_contact_number }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Owner email *</label>
                  <input v-model="form.owner_email" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.owner_email" class="text-red-500 text-sm mt-1">{{ form.errors.owner_email }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Representative name</label>
                  <input v-model="form.representative_name" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.representative_name" class="text-red-500 text-sm mt-1">{{ form.errors.representative_name }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Representative contact *</label>
                  <input v-model="form.representative_contact" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.representative_contact" class="text-red-500 text-sm mt-1">{{ form.errors.representative_contact }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Representative email *</label>
                  <input v-model="form.representative_email" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.representative_email" class="text-red-500 text-sm mt-1">{{ form.errors.representative_email }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Official Gmail *</label>
                  <input v-model="form.official_email_gmail" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.official_email_gmail" class="text-red-500 text-sm mt-1">{{ form.errors.official_email_gmail }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Applied date *</label>
                  <input type="date" v-model="form.applied_date" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.applied_date" class="text-red-500 text-sm mt-1">{{ form.errors.applied_date }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Applicant name *</label>
                  <input v-model="form.applicant_name" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.applicant_name" class="text-red-500 text-sm mt-1">{{ form.errors.applicant_name }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Applicant signature *</label>
                  <input v-model="form.applicant_signature" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.applicant_signature" class="text-red-500 text-sm mt-1">{{ form.errors.applicant_signature }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Type of industry *</label>
                  <input v-model="form.type_of_industry" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.type_of_industry" class="text-red-500 text-sm mt-1">{{ form.errors.type_of_industry }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Address within JHZE *</label>
                  <input v-model="form.address_within_jhze" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.address_within_jhze" class="text-red-500 text-sm mt-1">{{ form.errors.address_within_jhze }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Company Mobile *</label>
                  <input v-model="form.company_mobile_number" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.company_mobile_number" class="text-red-500 text-sm mt-1">{{ form.errors.company_mobile_number }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Company Email *</label>
                  <input v-model="form.company_email" class="border p-2 rounded-lg shadow-sm w-full" />
                  <p v-if="form.errors.company_email" class="text-red-500 text-sm mt-1">{{ form.errors.company_email }}</p>
                </div>

                <div>
                  <label class="block text-xs mb-1">Category *</label>
                  <select v-model="form.category" class="border p-2 rounded-lg shadow-sm w-full">
                    <option disabled value="">-- Select Category --</option>
                    <optgroup label="Business Enterprise">
                      <option>Primary Business</option>
                      <option>Secondary Business</option>
                    </optgroup>
                    <optgroup label="Service Providers and Suppliers">
                      <option>Regular Service Provider/Supplier</option>
                      <option>Seasonal Service Provider</option>
                    </optgroup>
                    <optgroup label="Commercial Event Operators">
                      <option>Commercial Event Organizers/Promoters</option>
                      <option>Commercial Event Concessionaries</option>
                    </optgroup>
                    <optgroup label="Authorized Entrepreneurs">
                      <option>Vendors</option>
                      <option>Accommodation Providers</option>
                      <option>Trade Fair Organizers</option>
                    </optgroup>
                    <optgroup label="Residents">
                      <option>Residents WITHOUT Business</option>
                      <option>Residents WITH Business</option>
                    </optgroup>
                  </select>
                  <p v-if="form.errors.category" class="text-red-500 text-sm mt-1">{{ form.errors.category }}</p>
                </div>
                
              </div>
                 <Toaster  richColors />
              <div class="flex justify-end space-x-3 mt-8">
              
                <button @click="isAddModalOpen = false" class="px-5 py-2 border rounded-lg bg-gray-100 hover:bg-gray-200">Cancel</button>
                <button @click="saveLocator" class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow hover:bg-blue-700" :disabled="form.processing">
                  <span v-if="form.processing">Saving...</span>
                  <span v-else>Save</span>
                </button>
                
              </div>
               
            </DialogPanel>
          </div>
        </Dialog>
      </TransitionRoot>

      <!-- View Modal -->
      <TransitionRoot appear :show="isViewModalOpen" as="template">
        <Dialog as="div" class="relative z-10" @close="closeViewModal">
          <div class="fixed inset-0 bg-black/40" />
          <div class="fixed inset-0 flex items-center justify-center p-4">
            <DialogPanel class="bg-white rounded-xl shadow-2xl w-full max-w-2xl p-6">
              <h2 class="text-2xl font-bold mb-4">Locator Details</h2>
              <div v-if="selectedLocator">
                <p><strong>Locator Name:</strong> {{ selectedLocator.locator }}</p>
                <p><strong>Owner Name:</strong> {{ selectedLocator.owner }}</p>
                <p><strong>Owner Email:</strong> {{ selectedLocator.email }}</p>
                <p><strong>Category:</strong> {{ selectedLocator.category }}</p>
                <p><strong>Status:</strong> {{ selectedLocator.status ? 'Active' : 'Inactive' }}</p>
              </div>
              <div class="mt-6 text-right">
                <button @click="closeViewModal" class="px-5 py-2 border rounded-lg bg-gray-100 hover:bg-gray-200">Close</button>
              </div>
            </DialogPanel>
          </div>
        </Dialog>
      </TransitionRoot>
    </div>
  </AppLayout>
</template>

<style scoped>
/* Ensure toast text is prominent */
[data-sonner-toaster] {
  left: auto !important;
  bottom: auto !important;
}

.sonner-toast {
  
  --width: 300px !important;
}
</style>
