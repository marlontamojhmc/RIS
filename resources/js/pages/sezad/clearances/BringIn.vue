<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { ChevronDown } from 'lucide-vue-next'
import { Disclosure, DisclosureButton, DisclosurePanel } from '@headlessui/vue'
import { Link, usePage } from '@inertiajs/vue3'
import type { PageProps } from '@inertiajs/core'
import { Head } from '@inertiajs/vue3'
import Vue3EasyDataTable from 'vue3-easy-data-table';
import 'vue3-easy-data-table/dist/style.css';
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue'
import { ref, onMounted } from 'vue';

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

// ✅ Define role flags
const accreditationCenter = hasAccess(user, 1);
const customsClearanceCenter = hasAccess(user, 2);
const laborCenter = hasAccess(user, 3);
const oneStopActionCenter = hasAccess(user, 4);
const sezadManager = hasAccess(user, 5);

// ✅ Define full menu items
const fullNavItems = [
    {
        name: "One Stop Action Center",
        children: [
            { name: "Accreditation of Locators", href: "/accreditation" },
            { name: "Certificate of Registration", href: "/certificate" },
            { name: "Application for Import Permit", href: "/import-permit" },
            { name: "Declaration of Admission of Articles", href: "/declaration" },
            {
                name: "Clearance",
                children: [
                    { name: "Gatepass Clearance", href: "/clearance/gatepass" },
                    { name: "Bring-In Clearance", href: "/sezad/clearances/bring-in" },
                    { name: "Bring-Out Clearance", href: "/clearance/bring-out" },
                    { name: "Temporary Bring-Out Clearance", href: "/clearance/temp-bring-out" },
                    { name: "Overtime Clearance", href: "/clearance/overtime" },
                    { name: "Local Purchase Clearance", href: "/clearance/local-purchase" },
                    { name: "Vehicle Entry/Exit Pass", href: "/clearance/vehicle" },
                    { name: "Workforce Access Clearance", href: "/clearance/workforce" },
                ],
            },
        ],
    },
    {
        name: "Labor Center",
        children: [
            { name: "Employment Report", href: "/labor/employment" },
            { name: "ID Processing Request", href: "/labor/id-processing" },
            { name: "Manpower Request", href: "/labor/manpower" },
        ],
    },
    {
        name: "Custom Compliance",
        children: [
            { name: "Inspection Report", href: "/custom/inspection" },
            { name: "Inventory Report", href: "/custom/inventory" },
            { name: "Monitoring of Imported Articles", href: "/custom/monitoring" },
            { name: "Quarterly Report (Zone to Zone)", href: "/custom/quarterly" },
            { name: "Special Permit Fees (Imported/Local)", href: "/custom/fees" },
            { name: "Locators", href: "/custom/locators" },
            { name: "Sales Report", href: "/custom/sales" },
            { name: "Monitoring of Violations", href: "/custom/violations" },
            { name: "Overtime Request", href: "/custom/overtime" },
            { name: "Certification of Articles Brought In", href: "/custom/certification" },
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

// Props if data passed from backend
// const props = defineProps<{ bringInList: BringIn[] }>()

// Example mock data
interface BringInItem {
    bring_in_no: string;
    control_no: string;
    title: string;
    date_created: string;
    created_by: string;
    status: string; // MUST be present
    pic: string;
}

const headers = [
    { text: 'Bring-In No', value: 'bring_in_no', sortable: true },
    { text: 'Control No', value: 'control_no', sortable: true },
    { text: 'Title', value: 'title', sortable: true },
    { text: 'Date Created', value: 'date_created', sortable: true },
    { text: 'Created By', value: 'created_by', sortable: true },
    { text: 'Status', value: 'status', sortable: true },
    { text: 'Person-in-Charge', value: 'pic', sortable: true },
];

const items = ref<BringInItem[]>([
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-01',
        title: 'testing bring-in 1',
        date_created: '2025-10-23',
        created_by: 'Locator',
        status: 'new',
        pic: 'Engr. Cruz',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-02',
        title: 'Equipment Entry 2',
        date_created: '2025-10-23',
        created_by: 'Locator',
        status: '',
        pic: 'Engr. Arce',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-03',
        title: 'bring in 3',
        date_created: '2025-10-23',
        created_by: 'Admin User 1',
        status: 'submitted',
        pic: 'Engr. Juan',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-04',
        title: 'Equipment Entry 4',
        date_created: '2025-10-23',
        created_by: 'Admin User',
        status: 'reviewed',
        pic: 'Engr. paul',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-05',
        title: 'Equipment Entry 5',
        date_created: '2025-10-23',
        created_by: 'Admin User',
        status: 'approved',
        pic: 'Engr. john A.',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-06',
        title: 'Equipment Entry 6',
        date_created: '2025-10-23',
        created_by: 'Admin User',
        status: 'paid',
        pic: 'Engr. jp arce pogi',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-07',
        title: 'Equipment Entry 7',
        date_created: '2025-10-23',
        created_by: 'Admin User',
        status: 'issued',
        pic: 'Engr. jp arce pogi 7',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-08',
        title: 'Equipment Entry 8',
        date_created: '2025-10-23',
        created_by: 'Admin User',
        status: 'inspected',
        pic: 'Engr. jp arce 8',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-09',
        title: 'Equipment Entry 9',
        date_created: '2025-10-23',
        created_by: 'Admin User',
        status: 'acknowledged',
        pic: 'Engr. jp arce acknowledged',
    },
    {
        bring_in_no: 'BI-2025-01',
        control_no: '2025-10-09',
        title: 'Equipment Entry 9',
        date_created: '2025-10-23',
        created_by: 'test 10',
        status: 'retracted',
        pic: 'Engr. jp arce acknowledged',
    },
]);

// Modal logic
const showModal = ref(false)
const selectedItem = ref<any>(null)

const handleRowClick = (row: any) => {
    selectedItem.value = row
    showModal.value = true
}
// Inside <script setup lang="ts">

const statusClass = (status: string): string => {
    if (!status) return '!bg-gray-300 !text-gray-600' // Added !

    switch (status.toLowerCase()) {
        case 'new':
            return '!bg-green-300 !text-green-800' // Added !
        case 'submitted':
            return '!bg-blue-100 !text-blue-700' // Added !
        case 'reviewed':
            return '!bg-yellow-100 !text-yellow-700' // Added !
        case 'approved':
            return '!bg-green-100 !text-green-700' // Added !
        case 'paid':
            return '!bg-indigo-100 !text-indigo-700' // Added !
        case 'issued':
            return '!bg-purple-100 !text-purple-700' // Added !
        case 'inspected':
            return '!bg-teal-100 !text-teal-700' // Added !
        case 'acknowledged':
            return '!bg-gray-300 !text-gray-700' // Added !
        case 'retracted':
            return '!bg-red-200 !text-red-700' // Added !
        default:
            return '!bg-gray-300 !text-gray-600' // Added !
    }
}

// Tailwind status badge color helper


onMounted(() => {
    console.log('Items:', items.value);

    statusClass(items.value[0]?.status || '');
});

const logItem = (item: unknown): string => {
    // Cast to the expected type for console output clarity, or leave as 'unknown'
    // Log the raw, untyped item first
    console.log('--- Current Table Item RAW ---');
    console.log(item); // 🚨 This should now show the full object!

    if (item && typeof item === 'object' && 'status' in item) {
        // Only attempt to log status if the object and property exist
        console.log('Status Value:', (item as BringInItem).status);
    } else {
        console.log('Status Value: NOT FOUND OR ITEM IS NULL/UNDEFINED');
    }
    console.log('------------------------------');
    return '';
};

const logIndexItem = (index: number): string => {
    const itemByIndex = items.value[index];
    console.log(`--- Item at Index ${index} ---`);
    console.log(itemByIndex);
    console.log(`Status via Index: ${itemByIndex?.status}`);
    console.log('------------------------------');
    return '';
};
const searchValue = ref('');

</script>

<template>
    <AppLayout>
        <main class="flex-1 bg-gray-100 min-h-screen p-0">
            <!-- Header inside main -->
            <header
                class="w-full bg-white text-black flex flex-col md:flex-row items-center justify-between shadow-md rounded-none px-4 py-3">

                <!-- Left: Title -->
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">BRING-IN CLEARANCES</h1>
                </div>

                <!-- Navigation on the right -->
                <nav class="flex flex-wrap items-center justify-end space-x-4">
                    <Disclosure v-for="item in navItems" :key="item.name" v-slot="{ open }" as="div" class="relative">
                        <DisclosureButton
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium hover:bg-gray-200 rounded-md transition">
                            <span>{{ item.name }}</span>
                            <ChevronDown class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" />
                        </DisclosureButton>

                        <DisclosurePanel
                            class="absolute right-0 mt-2 bg-gray-100 rounded-md shadow-lg w-56 p-2 space-y-1 z-50">
                            <template v-for="child in item.children" :key="child.name">
                                <div v-if="child.children" class="space-y-1">
                                    <p class="px-2 py-1 text-xs font-semibold text-gray-600">{{ child.name }}</p>
                                    <ul class="ml-2 space-y-1">
                                        <li v-for="sub in child.children" :key="sub.name">
                                            <Link :href="sub.href"
                                                class="block rounded-md px-3 py-1 text-sm hover:bg-gray-200 text-black">
                                            {{ sub.name }}
                                            </Link>
                                        </li>
                                    </ul>
                                </div>

                                <Link v-else :href="child.href"
                                    class="block rounded-md px-3 py-1 text-sm hover:bg-gray-200 text-black">
                                {{ child.name }}
                                </Link>
                            </template>
                        </DisclosurePanel>
                    </Disclosure>
                </nav>
            </header>

            <!-- Page content -->
            <section class="p-2">

                <Head title="Bring-In Clearances" />
                <div class="p-2">
                    <h2 class="text-xl font-semibold mb-4 text-gray-500">Bring-In Clearances List</h2>
                    <div class="mb-4">
                        <input v-model="searchValue" type="text" placeholder="Search table..."
                            class="w-full md:w-1/3 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition duration-150" />
                    </div>
                    <EasyDataTable :headers="headers" :items="items" :rows-per-page="10" show-index clickable
                        :search-value="searchValue" @click-row="handleRowClick"
                        class="rounded-lg shadow border border-gray-200">
                        <template #item-status="{ status, index }">
                            {{ logIndexItem(status) }}


                            <span class="px-2 py-1 text-xs font-medium rounded-full" :class="statusClass(status)">
                                <strong> {{ status.toUpperCase() || 'NO STATUS' }} </strong>


                            </span>

                        </template>
                    </EasyDataTable>

                    <!-- Modal -->
                    <TransitionRoot appear :show="showModal" as="template">
                        <Dialog as="div" class="relative z-10" @close="showModal = false">
                            <div class="fixed inset-0 bg-black bg-opacity-25" />

                            <div class="fixed inset-0 flex items-center justify-center p-4">
                                <DialogPanel class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl transition-all">
                                    <DialogTitle class="text-lg font-semibold mb-4 text-gray-900">
                                        Bring-In Details
                                    </DialogTitle>

                                    <div v-if="selectedItem" class="space-y-2 text-gray-700">
                                        <p><strong>Bring-In No:</strong> {{ selectedItem.bring_in_no }}</p>
                                        <p><strong>Control No:</strong> {{ selectedItem.control_no }}</p>
                                        <p><strong>Title:</strong> {{ selectedItem.title }}</p>
                                        <p><strong>Date Created:</strong> {{ selectedItem.date_created }}</p>
                                        <p><strong>Created By:</strong> {{ selectedItem.created_by }}</p>
                                        <p>
                                            <strong>Status:</strong>
                                            <span class="px-2 py-1 text-xs font-medium rounded-full"
                                                :class="statusClass(selectedItem.status)">
                                               <strong>{{ selectedItem.status.toUpperCase() || 'NO STATUS' }}</strong> 
                                            </span>
                                        </p>
                                        <p><strong>Person In-Charge:</strong> {{ selectedItem.pic }}</p>
                                    </div>

                                    <div class="mt-6 flex justify-end">
                                        <button type="button"
                                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-md text-gray-700"
                                            @click="showModal = false">
                                            Close
                                        </button>
                                    </div>
                                </DialogPanel>
                            </div>
                        </Dialog>
                    </TransitionRoot>
                </div>


            </section>
        </main>

    </AppLayout>
</template>
