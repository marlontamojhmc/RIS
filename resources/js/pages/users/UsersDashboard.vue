<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { usersDashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed, reactive, onMounted, watch } from 'vue';
import { Dialog, DialogPanel, DialogTitle, TransitionRoot } from '@headlessui/vue';
import { useForm } from '@inertiajs/vue3'
import { Toaster, toast } from 'vue-sonner'
import 'vue-sonner/style.css'
import { show } from '@/routes/two-factor';




// --- Dropdown state ---
const departments = ref<any[]>([]);
const divisions = ref<any[]>([]);
const roles = ref<any[]>([]);
const permissions = ref<any[]>([]);
const allPermissions = ref<any[]>([]); // keep all permissions for filtering
const props = defineProps({
    users: {
        type: Array,
        default: () => []
    },
    userFunctions: {
        type: Array,
        default: () => []
    }
})



const users = ref<any[]>(props.users || []); // Initialize users with props
// --- User state ---
const newUser = reactive({
    id: 0,
    employee_id: "",
    first_name: "",
    middle_name: "",
    last_name: "",
    suffix: "",
    email_address: "",
    department_id: "",
    division_id: "",
    role_id: "",
    permission_id: "",
    position: "",
    status: "Active",
    birth_date: "",
    sex: "",
    phone: "",
    address: {
        region: "",
        province: "",
        municipality: "",
        barangay: "",
        street: "",
    },
    user_function_id: "",
});

console.log(props.users);

// --- Load dropdown data ---
async function loadDepartments() {
    const res = await fetch("/departments");
    departments.value = await res.json();
    //console.log
}

async function loadRoles() {
    const res = await fetch("/roles");
    roles.value = await res.json();
}

async function loadPermissions() {
    const res = await fetch("/permissions");
    permissions.value = await res.json();
}

// --- Handle department → divisions ---
function onDepartmentChange() {
    const selectedDept = departments.value.find(
        (d) => d.id === newUser.department_id
    );
    divisions.value = selectedDept ? selectedDept.divisions : [];
    newUser.division_id = ""; // reset division when department changes
    newUser.user_function_id = ""; // reset division when department changes
}

async function loadRolesAndPermissions() {
    const res = await fetch("/roles");
    const data = await res.json();
    roles.value = data;

    // Flatten all permissions across roles
    const allPerms = data.flatMap((role: any) => role.permissions);
    // Remove duplicates by ID
    const uniquePerms = Array.from(new Map(allPerms.map((p: any) => [p.id, p])).values());
    permissions.value = uniquePerms;
}



// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Users Dashboard', href: usersDashboard().url },
];

// --- State ---



// Filters
const search = ref('');
const filterDept = ref('');
const filterDiv = ref('');

// Sorting
const sortField = ref('first_name');
const sortAsc = ref(true);
function sortBy(field: string) {
    if (sortField.value === field) sortAsc.value = !sortAsc.value;
    else {
        sortField.value = field;
        sortAsc.value = true;
    }
}

// --- Modal state ---
const showDetails = ref(false);
const showAddUser = ref(false);

const editableUser = reactive({
    id: null as number | null,
    employee_id: "",
    first_name: "",
    middle_name: "",
    last_name: "",
    suffix: "",
    email_address: "",
    status: "",
    department: "",
    division: "",
    roles: "",
    position: "",
    permission: "",
    user_function_id: "",
    //birth_date: "",
    sex: "",
    //phone: "",
    // address: {
    //     street: "",
    //     barangay: "",
    //     municipality: "",
    //     province: "",
    //     region: "",
    // },
});

// function setEditableUser(user: any) {
//   Object.assign(editableUser, user)

//   try {
//     editableUser.address = JSON.parse(user.address) || {}
//   } catch (e) {
//     console.warn("Invalid JSON in address:", user.address)
//     editableUser.address = {
//         street: "",
//         barangay: "",
//         municipality: "",
//         province: "",
//         region: ""
//     }
//   }
// }
// --- Functions ---
function openUser(user: any) {
    //Object.assign(editableUser, JSON.parse(JSON.stringify(user))); // deep copy

    Object.assign(editableUser, user)

    // Convert JSON string to object
    // if (typeof user.address === 'string') {
    //     try {
    //         editableUser.address = JSON.parse(user.address)
    //     } catch (e) {
    //         editableUser.address = {
    //             street: "",
    //             barangay: "",
    //             municipality: "",
    //             province: "",
    //             region: ""
    //         }
    //     }
    // }
    //console.log(editableUser);
    showDetails.value = true;
    // if (editableUser.address.region && editableUser.address.region !== "") {
    //     loadProvinces(editableUser.address.region);
    // }

    // if (editableUser.address.province && editableUser.address.province !== "") {
    //     loadMunicipalities(editableUser.address.province);
    // }

    // if (editableUser.address.municipality && editableUser.address.municipality !== "") {
    //     loadBarangays(editableUser.address.municipality);
    // }
}



function updateUser() {
    const index = users.value.findIndex((u) => u.id === editableUser.id);
    if (index !== -1) {
        users.value[index] = JSON.parse(JSON.stringify(editableUser));
    }
    showDetails.value = false;
}



const form = useForm({
    employee_id: '',
    first_name: '',
    middle_name: '',
    last_name: '',
    suffix: '',
    email_address: '',
    department_id: '',
    division_id: '',
    role_id: '',
    permission_id: '',
    position: '',
    status: '1',
    user_function_id: '',
    // birth_date: '',
    sex: '',
    // phone: '',
    // address: {
    //     region: '',
    //     province: '',
    //     municipality: '',
    //     barangay: '',
    //     street: '',
    // },
})

// save function
function addUser() {
    form.department_id = newUser.department_id;
    form.division_id = newUser.division_id;

    form.post('/users/addUser', {
        onSuccess: () => {
            toast.success('✅ User saved successfully!', {
                class: 'text-lg px-6 py-4 w-[400px]',
                duration: 1000, // 2 seconds before reload
            });

            // Wait a bit for toast to show before refreshing
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        },
        onError: (errors) => {
            console.error(errors);
            toast.error('⚠️ Validation failed. Please check your inputs.', {
                class: 'text-lg px-6 py-4 w-[400px]'
            });
        },
    });
}



const currentPage = ref(1);
const pageSize = ref(5);

const filteredUsers = computed(() => {
    return users.value
        .filter((u) => {
            const fullName = `${u.first_name} ${u.middle_name} ${u.last_name}`.toLowerCase();
            const matchesSearch =
                fullName.includes(search.value.toLowerCase()) ||
                (u.email_address && u.email_address.toLowerCase().includes(search.value.toLowerCase()));

            const matchesDept = filterDept.value ? u.department === filterDept.value : true;
            const matchesDiv = filterDiv.value ? u.division === filterDiv.value : true;

            return matchesSearch && matchesDept && matchesDiv;
        })
        .sort((a, b) => {
            let valA: string | number = '';
            let valB: string | number = '';

            switch (sortField.value) {
                case 'name':
                    valA = `${a.first_name} ${a.last_name}`.toLowerCase();
                    valB = `${b.first_name} ${b.last_name}`.toLowerCase();
                    break;
                case 'email_address':
                    valA = (a.email_address || '').toLowerCase();
                    valB = (b.email_address || '').toLowerCase();
                    break;
                case 'department':
                    valA = (a.department || '').toLowerCase();
                    valB = (b.department || '').toLowerCase();
                    break;
                case 'division':
                    valA = (a.division || '').toLowerCase();
                    valB = (b.division || '').toLowerCase();
                    break;
                case 'roles':
                    valA = (a.roles || '').toLowerCase();
                    valB = (b.roles || '').toLowerCase();
                    break;
                case 'status':
                    valA = (a.status || '').toLowerCase();
                    valB = (b.status || '').toLowerCase();
                    break;
                default:
                    valA = '';
                    valB = '';
            }

            if (valA < valB) return sortAsc.value ? -1 : 1;
            if (valA > valB) return sortAsc.value ? 1 : -1;
            return 0;
        });
});

const paginatedUsers = computed(() => {
    const start = (currentPage.value - 1) * pageSize.value;
    const end = start + pageSize.value;
    return filteredUsers.value.slice(start, end);
});

const totalPages = computed(() => Math.ceil(filteredUsers.value.length / pageSize.value));

function goToPage(page: number) {
    if (page >= 1 && page <= totalPages.value) currentPage.value = page;
}


// --- Location Data ---
const regions = ref<any[]>([]);
const provinces = ref<any[]>([]);
const municipalities = ref<any[]>([]);
const barangays = ref<any[]>([]);

// Fetch regions on load
onMounted(async () => {
    //const res = await fetch("https://psgc.cloud/api/regions");
    //regions.value = await res.json();
    await loadDepartments();
    await loadRoles();
    await loadPermissions();
    //console.log(editableUser.address)
    handleDepartmentChange();
    console.log(form);


});

// When region changes → fetch provinces
// async function loadProvinces(regionCode: string) {

//     const regionObject = JSON.parse(regionCode);
//     console.log("Region code:", regionObject.code);
//     console.log("Region name:", regionObject.name);
//     newUser.address.province = "";
//     newUser.address.municipality = "";
//     newUser.address.barangay = "";
//     municipalities.value = [];
//     barangays.value = [];

//     const res = await fetch(`https://psgc.cloud/api/regions/${regionObject.code}/provinces`);
//     provinces.value = await res.json();
//     console.log(provinces.value);
// }

// // When province changes → fetch municipalities
// async function loadMunicipalities(provinceCode: string) {
//     const provinceObject = JSON.parse(provinceCode);
//     newUser.address.municipality = "";
//     newUser.address.barangay = "";
//     barangays.value = [];

//     const res = await fetch(`https://psgc.cloud/api/provinces/${provinceObject.code}/cities-municipalities`);
//     municipalities.value = await res.json();
//     console.log(municipalities.value);

// }

// // When municipality changes → fetch barangays
// async function loadBarangays(muniCode: string) {
//     const muniObject = JSON.parse(muniCode);
//     newUser.address.barangay = "";
//     const res = await fetch(`https://psgc.cloud/api/cities-municipalities/${muniObject.code}/barangays`);
//     barangays.value = await res.json();
//     console.log(barangays.value);
// }


const userFunctions = ref<any[]>(props.userFunctions || []);



// Automatically set Division to “User Functions” when Department is 12 (Special Economic Zone)
const handleDepartmentChange = () => {
    if (editableUser.department == '12' || editableUser.department === 'Special Economic Zone') {
        editableUser.division = 'User Functions';
    }
};

const getFunctionName = (id: number | string) => {
    const found = userFunctions.value.find(f => f.id === Number(id))
    return found ? found.function : 'Unknown Function'
}
</script>


<template>

    <Head title="Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b px-6 py-4">
                    <h2 class="text-xl font-bold text-gray-800">👥 Users Dashboard</h2>


                    <Toaster position="top-right" richColors /> <!-- ✅ enables color themes -->


                    <button @click="showAddUser = true"
                        class="flex items-center gap-2 rounded-md bg-[#0F75BC] px-4 py-2 text-white shadow hover:bg-blue-700 transition">
                        ➕ Add User
                    </button>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap gap-4 p-6">
                    <input v-model="search" type="text" placeholder="🔍 Search by name or email"
                        class="w-64 rounded-md border px-3 py-2 shadow-sm focus:border-[#0F75BC] focus:ring-[#0F75BC]" />
                    <select v-model="filterDept" class="rounded-md border px-3 py-2 shadow-sm">
                        <option value="">All Departments</option>
                        <option>IT</option>
                        <option>HR</option>
                    </select>
                    <select v-model="filterDiv" class="rounded-md border px-3 py-2 shadow-sm">
                        <option value="">All Divisions</option>
                        <option>Development</option>
                        <option>Recruitment</option>
                    </select>
                </div>

                <!-- Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm m-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer"
                                    @click="sortBy('first_name')">
                                    Name
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer"
                                    @click="sortBy('email_address')">
                                    Email
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer"
                                    @click="sortBy('department')">
                                    Department
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer"
                                    @click="sortBy('division')">
                                    Division
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer"
                                    @click="sortBy('roles')">
                                    Role
                                </th>
                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 cursor-pointer"
                                    @click="sortBy('status')">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="user in paginatedUsers" :key="user.id"
                                class="cursor-pointer odd:bg-white even:bg-gray-50 hover:bg-blue-50"
                                @click="openUser(user)">
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ user.first_name }} {{ user.middle_name }} {{ user.last_name }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ user.email_address }}</td>
                                <td class="px-4 py-3">{{ user.department }}</td>
                                <td class="px-4 py-3">{{ user.division }}</td>
                                <td class="px-4 py-3">{{ user.roles }}</td>
                                <td>
                                    <span :class="user.status == 1 ? 'text-green-600' : 'text-red-600'">
                                        {{ user.status == 1 ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="flex justify-center items-center gap-2 p-4">
                        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-3 py-1 rounded border bg-gray-100 hover:bg-gray-200 disabled:opacity-50">
                            ‹ Prev
                        </button>

                        <button v-for="page in totalPages" :key="page" @click="goToPage(page)"
                            class="px-3 py-1 rounded border" :class="page === currentPage
                                ? 'bg-[#0F75BC] text-white border-[#0F75BC]'
                                : 'bg-white hover:bg-gray-100 text-gray-700 border-gray-300'">
                            {{ page }}
                        </button>

                        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                            class="px-3 py-1 rounded border bg-gray-100 hover:bg-gray-200 disabled:opacity-50">
                            Next ›
                        </button>
                    </div>
                </div>
            </div>


            <!-- User Details Modal (3-column layout) -->
            <TransitionRoot appear :show="showDetails" as="template">
                <Dialog as="div" class="relative z-50" @close="showDetails = false">
                    <div class="fixed inset-0 bg-black/40" />
                    <div class="fixed inset-0 flex items-center justify-center p-4">
                        <DialogPanel class="w-full max-w-5xl rounded-lg bg-white shadow-xl">
                            <!-- Header -->
                            <div class="flex items-center justify-between border-b px-6 py-4">
                                <DialogTitle class="text-lg font-bold">✏️ Edit User</DialogTitle>
                                <button @click="showDetails = false"
                                    class="text-gray-400 hover:text-gray-600">✕</button>
                            </div>

                            <!-- Form -->
                            <form v-if="editableUser" @submit.prevent="updateUser">
                                <!-- 2 Columns -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 text-sm">
                                    <!-- Column 1 -->
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block font-medium">Employee ID</label>
                                            <input v-model="editableUser.employee_id" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>
                                        <div>
                                            <label class="block font-medium">First Name</label>
                                            <input v-model="editableUser.first_name" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>
                                        <div>
                                            <label class="block font-medium">Middle Name</label>
                                            <input v-model="editableUser.middle_name" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>
                                        <div>
                                            <label class="block font-medium">Last Name</label>
                                            <input v-model="editableUser.last_name" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>
                                        <div>
                                            <label class="block font-medium">Suffix</label>
                                            <input v-model="editableUser.suffix" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>
                                        <div>
                                            <label class="block font-medium">Email</label>
                                            <input v-model="editableUser.email_address" type="email"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>
                                        <div>
                                            <label class="block font-medium">Sex</label>
                                            <select v-model="editableUser.sex"
                                                class="w-full rounded-md border-gray-300 shadow-sm">
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Column 2 -->
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Status</label>
                                            <select v-model="editableUser.status"
                                                class="w-full rounded-md border-gray-300 shadow-sm">
                                                <option :value="1">Active</option>
                                                <option :value="0">Inactive</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block font-medium">Department</label>
                                            <input v-model="editableUser.department" type="text"
                                                @change="handleDepartmentChange"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>

                                        <div>
                                            <label class="block font-medium">Division</label>
                                            <input v-model="editableUser.division" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>
                                        <div>
                                            <label class="block font-medium">User function</label>
                                            
                                            <select v-model="newUser.user_function_id"
                                                class="w-full rounded-md border-gray-300 shadow-sm"
                                                :disabled="Number(editableUser.department) == 12">
                                                <!-- Show current function name from DB -->
                                                <option disabled value="">
                                                    {{
                                                        editableUser.user_function_id
                                                            ? getFunctionName(editableUser.user_function_id)
                                                    : '-- Select User Function --'
                                                    }}
                                                </option>

                                                <!-- Show full list if department is 12 -->
                                                <option v-for="func in userFunctions" :key="func.id" :value="func.id">
                                                    {{ func.function }}
                                                </option>
                                            </select>

                                        </div>

                                        <div>
                                            <label class="block font-medium">Position</label>
                                            <input v-model="editableUser.position" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>

                                        <div>
                                            <label class="block font-medium">Role</label>
                                            <input v-model="editableUser.roles" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>

                                        <div>
                                            <label class="block font-medium">Permission</label>
                                            <input v-model="editableUser.permission" type="text"
                                                class="w-full rounded-md border-gray-300 shadow-sm" />
                                        </div>

                                        <!-- Commented out: Birth Date -->
                                        <!-- 
              <div>
                <label class="block font-medium">Birth Date</label>
                <input v-model="editableUser.birth_date" type="date"
                  class="w-full rounded-md border-gray-300 shadow-sm" />
              </div>
              -->

                                        <!-- Commented out: Phone -->
                                        <!-- 
              <div>
                <label class="block font-medium">Phone</label>
                <input v-model="editableUser.phone" type="text"
                  class="w-full rounded-md border-gray-300 shadow-sm" />
              </div>
              -->
                                    </div>
                                </div>

                                <!-- Footer -->
                                <div class="flex justify-end gap-3 border-t px-6 py-4 bg-white sticky bottom-0">
                                    <button type="button" @click="showDetails = false"
                                        class="px-4 py-2 rounded-md bg-gray-200 hover:bg-gray-300">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 rounded-md bg-[#0F75BC] text-white hover:bg-blue-700">
                                        Save Changes
                                    </button>
                                </div>
                            </form>
                        </DialogPanel>
                    </div>
                </Dialog>
            </TransitionRoot>

            <!-- Add User Modal -->
            <TransitionRoot appear :show="showAddUser" as="template">
                <Dialog as="div" class="relative z-50" @close="showAddUser = false">
                    <div class="fixed inset-0 bg-black/30" />
                    <div class="fixed inset-0 flex items-center justify-center p-2">
                        <DialogPanel class="w-full max-w-6xl rounded-lg bg-white p-4 shadow-xl text-sm">
                            <DialogTitle class="text-base font-semibold mb-3">Add User</DialogTitle>

                            <form @submit.prevent="addUser" class="grid grid-cols-3 gap-2">

                                <!-- BASIC INFO -->
                                <div class="col-span-3 font-medium text-gray-600 mb-1">Basic Information |
                                    <span class="text-red-500">*</span> Required
                                </div>

                                <div>
                                    <label class="block text-xs text-gray-500">Employee ID <span
                                            class="text-red-500">*</span></label>
                                    <input v-model="form.employee_id" required
                                        class="w-full border px-2 py-1 rounded" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Email <span
                                            class="text-red-500">*</span></label>
                                    <input v-model="form.email_address" required type="email"
                                        class="w-full border px-2 py-1 rounded" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Status <span
                                            class="text-red-500">*</span></label>
                                    <select v-model="form.status" class="w-full border px-2 py-1 rounded" required>
                                        <option disabled value="">Select</option>
                                        <option :value="1">Active</option>
                                        <option :value="0">Inactive</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">First Name <span
                                            class="text-red-500">*</span></label>
                                    <input v-model="form.first_name" required class="w-full border px-2 py-1 rounded" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Middle Name</label>
                                    <input v-model="form.middle_name" class="w-full border px-2 py-1 rounded" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Last Name <span
                                            class="text-red-500">*</span></label>
                                    <input v-model="form.last_name" required class="w-full border px-2 py-1 rounded" />
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Suffix</label>
                                    <input v-model="form.suffix" class="w-full border px-2 py-1 rounded" />
                                </div>
                                <!-- <div>
                                    <label class="block text-xs text-gray-500">Birth Date
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <input v-model="form.birth_date" type="date" class="w-full border px-2 py-1 rounded"
                                        required />
                                </div> -->
                                <div>
                                    <label class="block text-xs text-gray-500">Sex
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.sex" class="w-full border px-2 py-1 rounded" required>
                                        <option disabled value="">Select</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>

                                <!-- WORK DETAILS -->
                                <div class="col-span-3 font-medium text-gray-600 mt-2 mb-1">Work Details</div>
                                <div>
                                    <label class="block text-xs text-gray-500">Position<span
                                            class="text-red-500">*</span></label>
                                    <input v-model="form.position" class="w-full border px-2 py-1 rounded" required />
                                </div>
                                <!-- Department Select -->
                                <!-- Department Select -->
                                <div>
                                    <label class="block text-xs text-gray-500">Department
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="newUser.department_id" @change="onDepartmentChange"
                                        class="border p-2 rounded w-full" required>
                                        <option disabled value="">-- Select Department --</option>
                                        <option v-for="dept in departments" :key="dept.id" :value="dept.id">
                                            {{ dept.department_name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Division Select (always visible, disabled if none) -->
                                <div>
                                    <label class="block text-xs text-gray-500">
                                        Division
                                    </label>
                                    <!-- Otherwise show Division Dropdown -->
                                    <select class="border p-2 rounded w-full bg-gray-100" :disabled="!divisions.length">
                                        <option value="">
                                            {{ divisions.length ? '-- Select Division --' : 'No divisions available' }}
                                        </option>
                                        <option v-for="div in divisions" :key="div.id" :value="div.id">
                                            {{ div.division_name }}
                                        </option>
                                    </select>
                                </div>


                                <div>
                                    <label class="block text-xs text-gray-500">Role
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.role_id" class="w-full border px-2 py-1 rounded" required>
                                        <option value="">Select</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Permission
                                        <span class="text-red-500">*</span>
                                    </label>
                                    <select v-model="form.permission_id" class="w-full border px-2 py-1 rounded"
                                        required>
                                        <option value="">Select</option>
                                        <option v-for="perm in permissions" :key="perm.id" :value="perm.id">{{ perm.name
                                        }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">
                                        User Function
                                    </label>

                                    <select v-model="newUser.user_function_id"
                                        class="border p-2 rounded w-full bg-gray-100"
                                        :disabled="Number(newUser.department_id) !== 12" required>
                                        <option disabled value="">-- Select User Function --</option>
                                        <option v-for="func in userFunctions" :key="func.id" :value="func.id">
                                            {{ func.function }}
                                        </option>
                                    </select>

                                </div>
                                <!-- <div>
                                    <label class="block text-xs text-gray-500">Phone</label>
                                    <input v-model="form.phone" class="w-full border px-2 py-1 rounded" />
                                </div> -->

                                <!-- ADDRESS -->
                                <!-- <div class="col-span-3 font-medium text-gray-600 mt-2 mb-1">Address</div>
                                <div>
                                    <label class="block text-xs text-gray-500">Region <span
                                            class="text-red-500">*</span></label>
                                    <select v-model="form.address.region" @change="loadProvinces(form.address.region)"
                                        class="w-full border px-2 py-1 rounded" required>
                                        <option value="">Select</option>
                                        <option v-for="r in regions" :key="r.code"
                                            :value="JSON.stringify({ code: r.code, name: r.name })">{{ r.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Province <span
                                            class="text-red-500">*</span></label>
                                    <select v-model="form.address.province"
                                        @change="loadMunicipalities(form.address.province)"
                                        class="w-full border px-2 py-1 rounded" :disabled="!provinces.length" required>
                                        <option value="">Select</option>
                                        <option v-for="p in provinces" :key="p.code"
                                            :value="JSON.stringify({ code: p.code, name: p.name })">{{ p.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Municipality <span
                                            class="text-red-500">*</span></label>
                                    <select v-model="form.address.municipality"
                                        @change="loadBarangays(form.address.municipality)"
                                        class="w-full border px-2 py-1 rounded" :disabled="!municipalities.length"
                                        required>
                                        <option value="">Select</option>
                                        <option v-for="m in municipalities" :key="m.code"
                                            :value="JSON.stringify({ code: m.code, name: m.name })">{{ m.name }}
                                        </option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-500">Barangay <span
                                            class="text-red-500">*</span></label>
                                    <select v-model="form.address.barangay" class="w-full border px-2 py-1 rounded"
                                        :disabled="!barangays.length" required>">
                                        <option value="">Select</option>
                                        <option v-for="b in barangays" :key="b.code"
                                            :value="JSON.stringify({ code: b.code, name: b.name })">{{ b.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500">Street</label>
                                    <input v-model="form.address.street" class="w-full border px-2 py-1 rounded" />
                                </div> -->

                                <!-- ACTION BUTTONS -->
                                <div class="col-span-3 flex justify-end mt-3">
                                    <button type="button" @click="showAddUser = false"
                                        class="px-3 py-1 bg-gray-300 rounded-md mr-2 text-sm">Cancel</button>
                                    <!-- <button type="submit"
                                        class="px-3 py-1 bg-[#0F75BC] text-white rounded-md text-sm">Save</button> -->
                                    <button type="button" @click="addUser"
                                        class="px-3 py-1 bg-[#0F75BC] text-white rounded-md text-sm">
                                        Save
                                    </button>
                                </div>

                            </form>
                        </DialogPanel>
                    </div>
                </Dialog>
            </TransitionRoot>

        </div>
    </AppLayout>
</template>
