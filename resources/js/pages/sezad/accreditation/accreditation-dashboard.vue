<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ChevronDown } from 'lucide-vue-next'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { Link, usePage } from '@inertiajs/vue3'
import type { PageProps } from '@inertiajs/core'
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import ServiceProviderFormModal from './service-provider-supplier-form-modal.vue'
// Dummy stats — replace these with real backend props
const stats = ref({
  new: 12,
  renewal: 8,
  view: 5,
});

const goTo = (link: string) => {
  router.visit(link);
};

interface UserDetails {
  permission_id: number
  role_id: number
  department_id: number
  division_id: number
  user_function_id: number
}

interface AuthUser {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
  details?: UserDetails
}

interface CustomPageProps extends PageProps {
  auth: {
    user: AuthUser
  }
}

const page = usePage<CustomPageProps>()
const user = page.props.auth.user
const currentUrl = computed(() => page.url)
console.log('Current URL:', currentUrl.value);
const isActive = (href?: string) => {
  if (!href) return false;
  const normalizedHref = href.startsWith('/') ? href : `/${href}`;
  const result = currentUrl.value.startsWith(normalizedHref);

  console.log('➡️ Checking active state:', {
    href,
    normalizedHref,
    currentUrl: currentUrl.value,
    result,
  });

  return result;
};

const mobileOpen = ref(false);
const openDropdown = ref<string | null>(null);
function toggleDropdown(name: string) {
  openDropdown.value = openDropdown.value === name ? null : name;
}


// ✅ Helper function to check user details
const hasAccess = (user: AuthUser, functionId: number) => {
  const d = user?.details;
  return (
    d &&
    d.role_id === 2 &&
    d.permission_id === 2 &&
    d.department_id === 12 &&
    d.division_id === null &&
    d.user_function_id === functionId
  );
};

console.log('User Details:', user?.details, 'hasAccess:', {
  accreditationCenter: hasAccess(user, 1),
  customsClearanceCenter: hasAccess(user, 2),
  laborCenter: hasAccess(user, 3),
  oneStopActionCenter: hasAccess(user, 4),
  sezadManager: hasAccess(user, 5),
});


// ✅ Define role flags
const accreditationCenter = hasAccess(user, 1);
const customsClearanceCenter = hasAccess(user, 2);
const laborCenter = hasAccess(user, 3);
const oneStopActionCenter = hasAccess(user, 4);
const sezadManager = hasAccess(user, 5);

// ✅ Define full menu items
interface NavLink {
  name: string
  href?: string
  children?: NavLink[]
}

const fullNavItems: NavLink[] = [
  {
    name: "Accreditation Center",
    href: "/sezad/accreditation"
  },
  {
    name: "Authority to Operate",
    href: "/authority-to-operate"
  },
  {
    name: "One Stop Action Center",
    children: [
      {
        name: "Clearances",
        children: [
          { name: "Gate Clearance", href: "/clearance/gatepass" },
          { name: "Bring-In Clearance", href: "/sezad/clearances/bring-in" },
          { name: "Bring-Out Clearance", href: "/clearance/bring-out" },
          { name: "Temporary Bring-Out Clearance", href: "/clearance/temp-bring-out" },
          { name: "Local Purchase Clearance", href: "/clearance/temp-bring-out" },
          { name: "Storage Clearance", href: "/clearance/storage" },
          { name: "Workflow Access Clearance", href: "/clearance/workflow" },
        ],
      },
    ],
  },
  {
    name: "Custom Clearance Center",
    children: [
      {
        name: "Clearances",
        children: [
          { name: "Gate Clearance", href: "/clearance/gatepass" },
          { name: "Bring-In Clearance", href: "/sezad/clearances/bring-in" },
          { name: "Bring-Out Clearance", href: "/clearance/bring-out" },
          { name: "Temporary Bring-Out Clearance", href: "/clearance/temp-bring-out" },
          { name: "Local Purchase Clearance", href: "/clearance/temp-bring-out" },
          { name: "Storage Clearance", href: "/clearance/storage" },
          { name: "Workflow Access Clearance", href: "/clearance/workflow" },
        ],
      },
    ],
  }, {
    name: "Labor Center",
    children: [
      {
        name: "Clearances",
        children: [
          { name: "Gate Clearance", href: "/clearance/gatepass" },
          { name: "Bring-In Clearance", href: "/sezad/clearances/bring-in" },
          { name: "Bring-Out Clearance", href: "/clearance/bring-out" },
          { name: "Temporary Bring-Out Clearance", href: "/clearance/temp-bring-out" },
          { name: "Local Purchase Clearance", href: "/clearance/temp-bring-out" },
          { name: "Storage Clearance", href: "/clearance/storage" },
          { name: "Workflow Access Clearance", href: "/clearance/workflow" },
        ],
      },
    ],
  },
];

// ✅ For One Stop Action Center only
const oneStopActionCenterOnly = [
  {
    name: "One Stop Action Center",
    children: [

      {
        name: "Clearance",
        children: [
          { name: "Bring-In Clearance", href: "sezad/clearances/bring-in" },
          { name: "Bring-Out Clearance", href: "/clearance/bring-out" },
          { name: "Temporary Bring-Out Clearance", href: "/clearance/temp-bring-out" },
          { name: "Gatepass Clearance", href: "/clearance/gatepass" },
          { name: "Local Purchase Clearance", href: "/clearance/temp-bring-out" },
        ],
      },
    ],
  },
];

// ✅ Build the final navItems dynamically
let navItems: any[] = [];

if (sezadManager) {
  // Manager sees all
  navItems = fullNavItems;
} else if (oneStopActionCenter) {
  navItems = oneStopActionCenterOnly;
} else if (laborCenter) {
  navItems = fullNavItems.filter((i) => i.name === "Labor Center");
} else if (customsClearanceCenter) {
  navItems = fullNavItems.filter((i) => i.name === "Custom Compliance");
} else if (accreditationCenter) {
  navItems = fullNavItems.filter((i) => i.name === "One Stop Action Center");
}


const hasAccreditationAccess = accreditationCenter || sezadManager;
interface ServiceProvider {
  nameOfBusiness: string
  parentCompany: string
  natureOfContract: string
  tradeName: string
  email: string
  location: string
  contactPerson: string
  contactNumber: string
  accreditation: string
  status: string
  taxpayerName: string
  tin: string
  psicPrimary: string
  psicSecondary: string
  mainOffice: string
  documents: {
    name: string
    url: string
  }[]
}
const selectedItem = ref<ServiceProvider | null>(null)
const showAddModal = ref(false)
const showRenewModal = ref(false)


function refreshList() {
  // optionally refetch data from Laravel or update locally
}


function openRenew(item?: any) {
  // close the details modal first if it’s open
  selectedItem.value = null
  showRenewModal.value = true
}

// function closeRenew() {
//     showRenewModal.value = false
// }
function closeDetails() { selectedItem.value = null }

// // open add / renew modals
function openAdd() { showAddModal.value = true }

// function closeAdd() { showAddModal.value = false }

</script>
<style scoped>
/* Optional subtle animation */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

div[role='grid']>div {
  animation: fadeInUp 0.4s ease-in-out;
}
</style>

<template>
  <AppLayout>
    <main class="flex-1 bg-gray-100 min-h-screen p-0">
      <!-- Header inside main -->


      <!-- Navigation on the right -->
      <nav
        class="sticky top-0 left-0 right-0 z-40 bg-white shadow-md border-b border-gray-100 backdrop-blur-sm bg-opacity-95">
        <div class="flex items-center justify-between px-4 py-3 md:px-8 max-w-7xl mx-auto">
          <!-- Left: SEZAD -->
          <div class="flex items-center space-x-2">
            <h1 class="text-xl font-bold text-blue-700 cursor-pointer">
              <Link href="/sezad">SEZAD</Link>
            </h1>
          </div>

          <!-- Right: Desktop Navigation -->
          <div class="hidden md:flex items-center space-x-4">
            <div v-for="item in fullNavItems" :key="item.name" class="relative group">
              <!-- Regular Link -->
              <Link v-if="!item.children" :href="item.href"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-md transition text-gray-700 hover:bg-blue-100 hover:text-blue-700">
              {{ item.name }}
              </Link>

              <!-- Dropdown -->
              <div v-else class="relative">
                <button type="button"
                  class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-blue-100 hover:text-blue-700 rounded-md transition"
                  @click="toggleDropdown(item.name)">
                  <span>{{ item.name }}</span>
                  <ChevronDown class="w-4 h-4 transition-transform duration-300"
                    :class="{ 'rotate-180': openDropdown === item.name }" />
                </button>

                <div v-show="openDropdown === item.name"
                  class="absolute right-0 mt-2 bg-white rounded-md shadow-lg border border-gray-100 w-56 p-2 z-50">
                  <template v-for="child in item.children" :key="child.name">
                    <div v-if="child.children" class="space-y-1">
                      <p class="px-3 py-1 text-xs font-semibold text-gray-600 border-b border-gray-100">
                        {{ child.name }}
                      </p>
                      <ul class="ml-2 space-y-1 mt-1">
                        <li v-for="sub in child.children" :key="sub.name">
                          <Link :href="sub.href"
                            class="block rounded-md px-3 py-1 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition">
                          {{ sub.name }}
                          </Link>
                        </li>
                      </ul>
                    </div>

                    <Link v-else :href="child.href"
                      class="block rounded-md px-3 py-1 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition">
                    {{ child.name }}
                    </Link>
                  </template>
                </div>
              </div>
            </div>
          </div>

          <!-- Mobile Menu Button -->
          <button @click="mobileOpen = !mobileOpen"
            class="md:hidden flex items-center justify-center w-10 h-10 rounded-md hover:bg-blue-50 transition">
            <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
              viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Mobile Dropdown -->
        <div :class="[
          'md:hidden border-t border-gray-100 px-4 pb-3 bg-white',
          mobileOpen ? 'block' : 'hidden'
        ]">
          <div v-for="item in fullNavItems" :key="item.name" class="py-1">
            <Link v-if="!item.children" :href="item.href"
              class="block px-3 py-2 rounded-md text-gray-700 hover:bg-blue-50 hover:text-blue-700">
            {{ item.name }}
            </Link>

            <div v-else>
              <button @click="toggleDropdown(item.name)"
                class="flex justify-between w-full px-3 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-md">
                {{ item.name }}
                <ChevronDown class="w-4 h-4 transition-transform"
                  :class="{ 'rotate-180': openDropdown === item.name }" />
              </button>
              <div v-show="openDropdown === item.name" class="pl-5 mt-1 space-y-1">
                <Link v-for="child in item.children" :key="child.name" :href="child.href"
                  class="block px-3 py-1 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-md">
                {{ child.name }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </nav>



      <!-- Page content -->
      <section class="p-6">

        <Head title="Accreditation Dashboard" />

        <div class="mx-auto max-w-7xl p-6">
          <!-- Title -->
          <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Accreditation Dashboard</h1>
            <p class="text-gray-500">Monitor and manage all accreditation applications</p>
          </div>

          <!-- Cards Section -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mt-10">

            <!-- 🟦 Service Providers / Suppliers -->
            <div class="relative group h-48" @click="goTo('accreditation/service-provider')">
              <!-- Card content -->
              <div
                class="cursor-pointer bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 shadow-lg hover:shadow-2xl rounded-2xl p-6 transition transform hover:-translate-y-1 hover:from-blue-100 hover:to-blue-200 h-full flex flex-col justify-between">
                <div class="flex items-center justify-between">
                  <h2 class="text-lg font-semibold text-gray-700 group-hover:text-blue-700">
                    Service Providers / Suppliers
                  </h2>
                  <div class="bg-blue-200 p-3 rounded-xl">
                    <i class="fa-solid fa-briefcase text-blue-600 text-2xl"></i>
                  </div>
                </div>
                <div class="text-sm text-gray-500">Accreditation Options</div>
              </div>

              <!-- Hover dropdown -->
              <div
                class="absolute left-0 right-0 mt-3 bg-white border border-gray-100 shadow-xl rounded-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-10">
                <div class="p-4 space-y-2">
                  <div @click.stop="openAdd"
                    class="cursor-pointer px-3 py-2 rounded-md text-blue-700 hover:bg-blue-50 hover:text-blue-900 transition">
                    <i class="fa-solid fa-plus-circle mr-2 text-blue-500"></i> New Accreditation
                  </div>
                  <div @click.stop="openRenew"
                    class="cursor-pointer px-3 py-2 rounded-md text-green-700 hover:bg-green-50 hover:text-green-900 transition">
                    <i class="fa-solid fa-rotate-right mr-2 text-green-500"></i> Re-accreditation
                  </div>
                </div>
              </div>
            </div>

            <!-- 🟩 Commercial Event Operators / Trade Fair Organizers -->
            <div @click="goTo('accreditation/event-operator')"
              class="cursor-pointer bg-gradient-to-br from-green-50 to-green-100 border border-green-200 shadow-lg hover:shadow-2xl rounded-2xl p-6 transition transform hover:-translate-y-1 hover:from-green-100 hover:to-green-200 h-48 flex flex-col justify-between">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-700 hover:text-green-700">
                  Commercial Event Operators & Trade Fair Organizers
                </h2>
                <div class="bg-green-200 p-3 rounded-xl">
                  <i class="fa-solid fa-store text-green-600 text-2xl"></i>
                </div>
              </div>
              <p class="text-sm text-gray-500">Including Concessionaire/s</p>
            </div>

            <!-- 🟨 Vendors / Micro-entrepreneurs -->
            <div class="relative group h-48" @click="goTo('accreditation/vendor')">
              <div
                class="cursor-pointer bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 shadow-lg hover:shadow-2xl rounded-2xl p-6 transition transform hover:-translate-y-1 hover:from-yellow-100 hover:to-yellow-200 h-full flex flex-col justify-between">
                <div class="flex items-center justify-between">
                  <h2 class="text-lg font-semibold text-gray-700 group-hover:text-yellow-700">
                    Vendors /
                    <br />
                    Micro-entrepreneurs
                  </h2>
                  <div class="bg-yellow-200 p-3 rounded-xl">
                    <i class="fa-solid fa-cart-shopping text-yellow-600 text-2xl"></i>
                  </div>
                </div>
                <div class="text-sm text-gray-500">Accreditation Options</div>
              </div>

              <!-- Hover dropdown -->
              <div
                class="absolute left-0 right-0 mt-3 bg-white border border-gray-100 shadow-xl rounded-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-10">
                <div class="p-4 space-y-2">
                  <div @click.stop="openAdd"
                    class="cursor-pointer px-3 py-2 rounded-md text-blue-700 hover:bg-blue-50 hover:text-blue-900 transition">
                    <i class="fa-solid fa-plus-circle mr-2 text-blue-500"></i> New Accreditation
                  </div>
                  <div @click.stop="openRenew"
                    class="cursor-pointer px-3 py-2 rounded-md text-green-700 hover:bg-green-50 hover:text-green-900 transition">
                    <i class="fa-solid fa-rotate-right mr-2 text-green-500"></i> Re-accreditation
                  </div>
                </div>
              </div>
            </div>


            <!-- 🟥 Provisional Grant -->
            <div @click="goTo('accreditation/provisional')"
              class="cursor-pointer bg-gradient-to-br from-red-50 to-red-100 border border-red-200 shadow-lg hover:shadow-2xl rounded-2xl p-6 transition transform hover:-translate-y-1 hover:from-red-100 hover:to-red-200 h-48 flex flex-col justify-between">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-700 hover:text-red-700">
                  Provisional Grant
                </h2>
                <div class="bg-red-200 p-3 rounded-xl">
                  <i class="fa-solid fa-hourglass-half text-red-600 text-2xl"></i>
                </div>
              </div>
              <p class="text-sm text-gray-500">Pending or Temporary Approval</p>
            </div>
          </div>

        </div>
        <ServiceProviderFormModal v-if="showAddModal" mode="add" @close="showAddModal = false" @saved="refreshList" />

        <!-- RENEW modal -->
        <ServiceProviderFormModal v-if="showRenewModal" mode="renew" :initialData="selectedItem || undefined"
          @close="showRenewModal = false" @saved="refreshList" />
      </section>
    </main>

  </AppLayout>
</template>
