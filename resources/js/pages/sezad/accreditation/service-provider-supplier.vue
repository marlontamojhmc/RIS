<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import Vue3EasyDataTable from 'vue3-easy-data-table'
import 'vue3-easy-data-table/dist/style.css'
import { ref, computed, nextTick } from 'vue'
import { ChevronDown } from 'lucide-vue-next'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { Link, usePage } from '@inertiajs/vue3'
import type { PageProps } from '@inertiajs/core'
import ServiceProviderFormModal from './service-provider-supplier-form-modal.vue'



// Dummy stats — replace these with real backend props
const stats = ref({
    new: 12,
    renewal: 8,
    view: 5,
});



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



// Search / sort
const search = ref('')
const sortBy = ref('')
const sortType = ref('')

// sample items (reactive)
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

const items = ref<ServiceProvider[]>([
    {
        nameOfBusiness: 'ABC Supplies',
        parentCompany: 'ABC Group',
        natureOfContract: 'Maintenance',
        tradeName: 'ABC Supply Co.',
        email: 'info@abc.com',
        location: 'Zone 2',
        contactPerson: 'Juan Dela Cruz',
        contactNumber: '0917-123-4567',
        accreditation: '2025-0001',
        status: 'New',
        taxpayerName: 'Juan Dela Cruz',
        tin: '123-456-789',
        psicPrimary: 'Construction',
        psicSecondary: 'Supply',
        mainOffice: 'Baguio City, Philippines',
        documents: [{ name: "Business Permit.pdf", url: "#" }],
    },
    {
        nameOfBusiness: 'XYZ Services',
        parentCompany: 'XYZ Holdings',
        natureOfContract: 'Consulting',
        tradeName: 'XYZ Consulting',
        email: 'contact@xyz.com',
        location: 'Zone 1',
        contactPerson: 'Maria Santos',
        contactNumber: '0918-555-5555',
        accreditation: '2025-0002',
        status: 'Reviewed',
        taxpayerName: 'Maria Santos',
        tin: '987-654-321',
        psicPrimary: 'Consultancy',
        psicSecondary: 'Training',
        mainOffice: 'Makati City, Philippines',
        documents: [{ name: "Mayor's Permit.pdf", url: "#" }],
    },
    {
        nameOfBusiness: 'Metro Vendors',
        parentCompany: 'Metro Corp',
        natureOfContract: 'Supply',
        tradeName: 'Metro Supplies',
        email: 'metro@corp.com',
        location: 'Zone 3',
        contactPerson: 'Carlos Reyes',
        contactNumber: '0908-321-4567',
        accreditation: '2025-0003',
        status: 'Retracted',
        taxpayerName: 'Carlos Reyes',
        tin: '555-444-333',
        psicPrimary: 'Supply',
        psicSecondary: 'Retail',
        mainOffice: 'Cebu City, Philippines',
        documents: [{ name: "DTI.pdf", url: "#" }],
    },
])

// Table headers
const headers = [
    { text: 'Name of Business', value: 'nameOfBusiness', sortable: true },
    { text: 'Parent Company', value: 'parentCompany', sortable: true },
    { text: 'Nature of Contract', value: 'natureOfContract', sortable: true },
    { text: 'Trade Name', value: 'tradeName' },
    { text: 'Email', value: 'email' },
    { text: 'Location (JHSEZ)', value: 'location' },
    { text: 'Contact Person', value: 'contactPerson' },
    { text: 'Contact Number', value: 'contactNumber' },
    { text: 'Accreditation', value: 'accreditation' },
    { text: 'Remarks', value: 'remarrks' },
    { text: 'Status', value: 'status', sortable: true },
]

// computed filtered items by search
const filteredItems = computed(() =>
    items.value.filter((it) =>
        Object.values(it).join(' ').toLowerCase().includes(search.value.toLowerCase())
    )
)

// status -> classes
const statusClass = (status = ''): string => {
    if (!status) return 'bg-gray-200 text-gray-700'
    switch (status.toLowerCase()) {
        case 'new':
            return 'bg-green-200 text-green-800'
        case 'submitted':
            return 'bg-blue-100 text-blue-700'
        case 'reviewed':
            return 'bg-yellow-100 text-yellow-700'
        case 'approved':
            return 'bg-green-100 text-green-700'
        case 'paid':
            return 'bg-indigo-100 text-indigo-700'
        case 'issued':
            return 'bg-purple-100 text-purple-700'
        case 'inspected':
            return 'bg-teal-100 text-teal-700'
        case 'acknowledged':
            return 'bg-gray-100 text-gray-700'
        case 'retracted':
            return 'bg-red-200 text-red-700'
        default:
            return 'bg-gray-200 text-gray-700'
    }
}

// modal logic
const selectedItem = ref<ServiceProvider | null>(null)
const showAddModal = ref(false)
const showRenewModal = ref(false)


function refreshList() {
  // optionally refetch data from Laravel or update locally
}

function openDetails(item: any, event?: MouseEvent) {
    // if the user clicked a button or link inside the row, don’t open the details modal
    if (event && (event.target as HTMLElement).closest('button, a')) return
    selectedItem.value = item
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
function openAdd() { 
    selectedItem.value = null  // clear previous selection
  showAddModal.value = false // ensure reactivity triggers
  nextTick(() => {
    showAddModal.value = true
  })}

// function closeAdd() { showAddModal.value = false }


// dummy actions
const retract = () => {
    // example: set status to 'Retracted'
    if (selectedItem.value) {
        selectedItem.value.status = 'Retracted'
    }
    closeDetails()
}
const reviewed = () => {
    if (selectedItem.value) {
        selectedItem.value.status = 'Reviewed'
    }
    closeDetails()
}

// navigation placeholder
const goTo = (link: string) => router.visit(link)

const addModalId = ref(0)

</script>

<template>
    <AppLayout>
        <main class="relative bg-white shadow-md border-b border-gray-100">



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
                                            <p
                                                class="px-3 py-1 text-xs font-semibold text-gray-600 border-b border-gray-100">
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
                        <svg v-if="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
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

                <Head title="Service Providers / Suppliers" />

                <div class="max-w-7xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h1 class="text-2xl font-bold text-[#0F75BC]">Service Providers / Suppliers</h1>

                        <div class="flex gap-2">
                            <!-- New opens add modal -->
                            <button @click="openAdd"
                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                + New
                            </button>

                            <!-- Re-New opens renew modal -->
                            <button @click="openRenew"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Re-New
                            </button>
                        </div>
                    </div>

                    <!-- search -->
                    <div class="mb-4">
                        <input v-model="search" type="text" placeholder="🔍 Search service providers..."
                            class="w-full sm:w-1/2 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-400" />
                    </div>

                    <!-- table -->
                    <EasyDataTable :headers="headers" :items="filteredItems" :rows-per-page="10" show-index clickable
                        @click-row="openDetails" class="rounded-lg shadow border border-gray-200">
                        <!-- FIXED slot name and data binding -->
                        <template #item-status="{ status }">
                            <span class="px-2 py-1 text-xs font-medium rounded-full" :class="statusClass(status)">
                                <strong>{{ status?.toUpperCase() || 'NO STATUS' }}</strong>
                            </span>
                        </template>
                    </EasyDataTable>
                    <p class="text-sm text-gray-500 mt-3 text-right">Showing {{ filteredItems.length }} result(s)</p>
                </div>

                <!-- DETAILS modal (transparent bg) -->
                <transition name="fade">
                    <div v-if="selectedItem"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm">
                        <div class="w-full max-w-3xl bg-white/95 rounded-xl p-6 shadow-xl overflow-auto">
                            <div class="flex items-start justify-between mb-4">
                                <h2 class="text-xl font-semibold text-[#0F75BC]">{{ selectedItem.nameOfBusiness }}</h2>
                                <button @click="closeDetails" class="text-gray-500 hover:text-gray-700">✕</button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p><strong>Parent Company:</strong> {{ selectedItem.parentCompany }}</p>
                                    <p><strong>Nature of Contract:</strong> {{ selectedItem.natureOfContract }}</p>
                                    <p><strong>Trade Name:</strong> {{ selectedItem.tradeName }}</p>
                                    <p><strong>Email:</strong> {{ selectedItem.email }}</p>
                                    <p><strong>Location (JHSEZ):</strong> {{ selectedItem.location }}</p>
                                </div>

                                <div>
                                    <p><strong>Contact Person:</strong> {{ selectedItem.contactPerson }}</p>
                                    <p><strong>Contact Number:</strong> {{ selectedItem.contactNumber }}</p>
                                    <p><strong>Accreditation:</strong> {{ selectedItem.accreditation }}</p>
                                    <p><strong>Status:</strong>
                                        <span class="ml-2 px-2 py-0.5 rounded text-sm"
                                            :class="statusClass(selectedItem.status)">
                                            {{ selectedItem.status }}
                                        </span>
                                    </p>
                                </div>

                                <hr class="col-span-2 my-3" />

                                <div>
                                    <p><strong>Taxpayer’s Name:</strong> {{ selectedItem.taxpayerName }}</p>
                                    <p><strong>TIN:</strong> {{ selectedItem.tin }}</p>
                                    <p><strong>PSIC Primary:</strong> {{ selectedItem.psicPrimary }}</p>
                                    <p><strong>PSIC Secondary:</strong> {{ selectedItem.psicSecondary }}</p>
                                    <p><strong>Main Office Address:</strong> {{ selectedItem.mainOffice }}</p>
                                </div>

                                <div>
                                    <p class="font-semibold">Uploaded Documents:</p>
                                    <ul class="list-disc list-inside text-blue-600">
                                        <li v-for="(doc, i) in selectedItem.documents" :key="i">
                                            <a :href="doc.url" target="_blank" class="hover:underline">{{ doc.name
                                            }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 mt-6">
                                <button @click="closeDetails" class="px-4 py-2 bg-gray-200 rounded-md">Close</button>
                                <button @click="retract"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md">Retract</button>
                                <button @click="reviewed"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md">Reviewed</button>
                            </div>
                        </div>
                    </div>
                </transition>

                <ServiceProviderFormModal v-if="showAddModal" mode="add" @close="showAddModal = false"
                    @saved="refreshList" />

                <!-- RENEW modal -->
                <ServiceProviderFormModal v-if="showRenewModal" mode="renew":initialData="selectedItem || undefined"
                    @close="showRenewModal = false" @saved="refreshList" />
            </section>
        </main>
    </AppLayout>
</template>

<style scoped>
/* fade */
.fade-enter-active,
.fade-leave-active {
    transition: opacity .2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
