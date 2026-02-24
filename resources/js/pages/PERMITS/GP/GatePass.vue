<script setup>
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, defineProps, reactive, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';
const page = usePage();
const toast = useToast();

const props = defineProps({
    application: { type: Array, required: true },
    user: { type: Object, required: true },
    application_id: { type: Number, required: true },
    approver_group_id: { type: Number, required: true },
    application_form_number: { type: Number, required: true },
    permitClearanceFees: { type: Array, required: true },
    form_id: { type: Number, required: true },
});
const validity = ref('1');
const validityList = [
    { value: '1', label: '1 Day' },
    { value: '5', label: '5 Days' },
    { value: '20', label: '20 Days' },
];
const saving = ref(false);
const showModal = ref(false);
const newRow = reactive({
    marksBag: '',
    quantity: '',
    description: '',
    declaredValue: null,
});
const fileInputs = reactive({}); // refs for file inputs

const form = reactive({
    application_form_id: props.application_id,
    title: props.application.form_title,
    approver_group_id: props.approver_group_id,
    documentCode: '',
    form_id: props.form_id,
    controlNo: '',
    gcNo: props.application_form_number,
    clearanceTo: props.user.name,
    table: [],
    selectedFeeId: null,
    selectedValidity: '',
    amount: '',
    siNumber: '',
    date: '',
    deliveryDate: '',
    authorizations: [],
    checklist: [],
    checklistFiles: {}, // file objects keyed by checklist item
});

const authorizations = [
    'Authority to Operate',
    'Accreditation',
    'SEZ/OSAC Clearance',
    'CDO Clearance',
];
const checklist = [
    'Commercial/Sales Invoice',
    'Official Receipt/Delivery Receipt',
    'Bill of Lading/Airway Bill',
    'Pro-forma Invoice/Quotation',
    'Affidavit/Declaration of Value',
    'Others',
];

const addRowFromModal = async () => {
    if (saving.value) return;
    saving.value = true;
    try {
        const response = await axios.post('/loctr/articles', {
            application_form_id: form.application_form_id,
            marks_and_number: newRow.marksBag,
            qty: newRow.quantity,
            detailed_description_of_article: newRow.description,
            price: newRow.declaredValue,
        });
        form.table.push({ ...response.data, saved: true });
        showModal.value = false;
        resetModal();
    } catch (error) {
        console.error(error.response?.data || error);
    } finally {
        saving.value = false;
    }
};

const removeRow = async (index, item) => {
    if (!item.id) {
        form.table.splice(index, 1);
        return;
    }
    try {
        await axios.delete(`/loctr/articles/${item.id}`);
        form.table.splice(index, 1);
    } catch (error) {
        console.error(error.response?.data || error);
        alert('Failed to delete row. Please try again.');
    }
};

const handleFileUpload = (event, key) => {
    if (event.target.files.length > 0) {
        form.checklistFiles[key] = event.target.files[0];
    }
};

const submitForm = async () => {
    saving.value = true;
    const formData = new FormData();

    // Append form JSON without files
    const formCopy = { ...form, checklistFiles: {} }; // exclude files
    formData.append('form', JSON.stringify(formCopy));

    // Append files separately
    for (const key in form.checklistFiles) {
        if (form.checklistFiles[key]) {
            formData.append(`files[${key}]`, form.checklistFiles[key]);
        }
    }

    try {
        const res = await axios.post('/permits/gatepass/submit', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        console.log(res.data);
        toast.success(`${form.title} Permit submitted successfully!`);
    } catch (err) {
        console.error(err);
        alert('Submission failed');
    } finally {
        saving.value = false;
    }
};
const filteredPermitFees = computed(() => {
    return props.permitClearanceFees.filter(
        (fee) => String(fee.value) === String(validity.value),
    );
});
watch(validity, (newVal) => {
    const index = validityList.findIndex((item) => item.value === newVal);
    console.log('Selected index:', index, 'Selected value:', newVal);
    form.selectedValidity = ''; // Reset selected validity when radio changes
}); //ref()
watch(
    () => form.selectedValidity,
    (newId) => {
        console.log('id', newId);
        const fee = props.permitClearanceFees.find((f) => f.id === newId);
        form.amount = fee ? `₱${Number(fee.price).toFixed(2)}` : '';
        form.selectedFeeId = newId; // Store selected fee ID in form data
        console.log('fee', form.amount);
    },
);

// watch(
//     () => form.selectedFeeId,
//     (newId) => {
//         console.log(newId);
//         const fee = props.permitClearanceFees.find((f) => f.id === newId);
//         form.amount = fee ? `₱${Number(fee.price).toFixed(2)}` : '';
//     },
// );
console.log(props.permitClearanceFees);
</script>
<template>
    <form
        @submit.prevent="submitForm"
        class="mx-auto max-w-6xl space-y-6 border border-gray-200 bg-white p-6 shadow-sm"
    >
        <!-- Header -->
        <div class="flex justify-between border-b pb-2">
            <!-- Left: Company Name -->
            <div class="flex-shrink-0">
                <input
                    v-model="form.companyName"
                    disabled
                    placeholder="John Hay"
                    class="w-48 border-b border-gray-300 text-xl font-bold uppercase"
                />
            </div>

            <!-- Center: Title -->
            <div class="flex-1 text-center">
                <h2
                    class="m-0 text-2xl font-extrabold tracking-tight uppercase"
                >
                    {{ form.title }}
                </h2>
            </div>

            <!-- Right: Codes in 2 rows -->
            <div class="flex flex-shrink-0 flex-col gap-1 text-right text-sm">
                <!-- Top row: Document Code & Control No side by side -->
                <div class="flex justify-end gap-2">
                    <div>
                        <label
                            for="documentCode"
                            class="block leading-none font-medium text-gray-700"
                            >Document Code</label
                        >
                        <input
                            id="documentCode"
                            v-model="form.documentCode"
                            placeholder="Enter Document Code"
                            class="w-32 border-b border-gray-300 py-0 text-sm focus:border-blue-500 focus:outline-none"
                        />
                    </div>
                    <div>
                        <label
                            for="controlNo"
                            class="block leading-none font-medium text-gray-700"
                            >Control No.</label
                        >
                        <input
                            id="controlNo"
                            v-model="form.controlNo"
                            placeholder="Enter Control No."
                            class="w-32 border-b border-gray-300 py-0 text-sm focus:border-blue-500 focus:outline-none"
                        />
                    </div>
                </div>

                <!-- Bottom row: Form No alone -->
                <div>
                    <label
                        for="gcNo"
                        class="block leading-none font-medium text-gray-700"
                        >Form No.</label
                    >
                    <input
                        id="gcNo"
                        v-model="form.gcNo"
                        placeholder="Enter GC No."
                        class="w-32 border-b border-gray-300 py-0 text-sm focus:border-blue-500 focus:outline-none"
                    />
                </div>
            </div>
        </div>
        <!-- Clearance Granted To -->
        <div class="space-y-2 border-b pb-4">
            <p class="font-semibold">CLEARANCE is hereby granted to:</p>
            <input
                v-model="form.clearanceTo"
                placeholder="Company Name"
                class="w-full border-b border-gray-300 text-lg font-bold"
            />
            <p>
                to pass thru the
                <span class="font-semibold uppercase">GATE</span>
            </p>
        </div>

        <!-- Detailed Description Table -->
        <div class="overflow-x-auto border-b pb-4">
            <div class="mb-2 flex justify-between">
                <p class="font-semibold">Detailed Description of Articles</p>
                <button
                    @click.prevent="showModal = true"
                    class="rounded bg-green-600 px-4 py-1 text-white hover:bg-green-700"
                >
                    + Add Article
                </button>
            </div>
            <table class="min-w-full border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-1 text-left">
                            MARKS BAG NUMBER
                        </th>
                        <th class="border px-2 py-1 text-left">QUANTITY</th>
                        <th class="border px-2 py-1 text-left">
                            DETAILED DESCRIPTION OF ARTICLES
                        </th>
                        <th class="border px-2 py-1 text-left">
                            DECLARED VALUE
                        </th>
                        <th class="border px-2 py-1 text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="(item, index) in form.table"
                        :key="index"
                        :class="item.saved ? 'bg-green-50' : ''"
                    >
                        <td class="border px-2 py-1">{{ item.marksBag }}</td>
                        <td class="border px-2 py-1">{{ item.quantity }}</td>
                        <td class="border px-2 py-1">{{ item.description }}</td>
                        <td class="border px-2 py-1">
                            {{ item.declaredValue }}
                        </td>
                        <td class="space-x-1 border px-2 py-1 text-center">
                            <button
                                @click.prevent="removeRow(index, item)"
                                class="font-bold text-red-600 hover:text-red-800"
                            >
                                X
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal for Adding Row -->
        <div
            v-if="showModal"
            class="bg-opacity-50 fixed inset-0 z-50 flex items-center justify-center bg-black"
        >
            <div class="relative w-96 rounded bg-white p-6 shadow-lg">
                <h2 class="mb-4 text-lg font-bold">Add Table Row</h2>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-semibold"
                            >Marks Bag Number</label
                        >
                        <input
                            v-model="newRow.marksBag"
                            class="w-full border-b border-gray-300"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold"
                            >Quantity</label
                        >
                        <input
                            v-model="newRow.quantity"
                            class="w-full border-b border-gray-300"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold"
                            >Description</label
                        >
                        <input
                            v-model="newRow.description"
                            class="w-full border-b border-gray-300"
                        />
                    </div>
                    <div>
                        <label class="block text-sm font-semibold"
                            >Declared Value</label
                        >
                        <input
                            type="number"
                            v-model="newRow.declaredValue"
                            class="w-full border-b border-gray-300"
                        />
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-2">
                    <button
                        @click.prevent="showModal = false"
                        class="rounded bg-gray-300 px-4 py-1 hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button
                        @click.prevent="addRowFromModal"
                        :disabled="saving"
                        class="flex items-center justify-center gap-2 rounded bg-green-600 px-4 py-1 text-white hover:bg-green-700 disabled:opacity-50"
                    >
                        <svg
                            v-if="saving"
                            class="h-4 w-4 animate-spin text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            ></path>
                        </svg>
                        <span>Save</span>
                    </button>
                </div>
            </div>
        </div>
        <!-- Permit Validity Dropdown -->
        <div class="space-y-2 border-b pb-4">
            <p class="font-semibold">Select Validity Period:</p>

            <div
                class="flex flex-col space-y-4 md:flex-row md:space-y-0 md:space-x-4"
            >
                <!-- Radio Buttons -->
                <div
                    class="flex justify-center p-2 md:border-r md:border-gray-700"
                >
                    <RadioGroup
                        v-model="validity"
                        class="flex flex-col space-y-2 md:flex-row md:space-y-0 md:space-x-4"
                    >
                        <div
                            v-for="(option, index) in validityList"
                            :key="index"
                            class="flex items-center space-x-2"
                        >
                            <RadioGroupItem
                                :id="'validity-' + option.value"
                                :value="option.value"
                            />
                            <label :for="'validity-' + option.value">{{
                                option.label
                            }}</label>
                        </div>
                    </RadioGroup>
                </div>

                <!-- Select Dropdown -->
                <div class="min-w-0 flex-1">
                    <select
                        v-model="form.selectedValidity"
                        class="w-full border-b border-gray-300 p-2"
                    >
                        <!-- Placeholder option -->
                        <option disabled value="">-- Select Validity --</option>

                        <!-- Options filtered by selected radio validity -->
                        <option
                            v-for="item in filteredPermitFees"
                            :key="item.id"
                            :value="item.id"
                        >
                            {{ item.title }}
                            <!-- — ₱{{ item.price }} -->
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <!-- <select
                v-model="form.selectedValidity"
                class="w-full border-b border-gray-300 p-2"
            >
                <option disabled value="">-- Select Validity --</option>
                <option
                    v-for="fee in permitClearanceFees"
                    :key="fee.id"
                    :value="fee.id"
                >
                    {{ fee.validity }} / {{ fee.title }}
                </option>
            </select> -->

        <!-- Fully Paid Under Section -->
        <div class="grid grid-cols-4 gap-4 border-b pb-4 text-sm">
            <div>
                <p class="font-semibold">SI NUMBER</p>
                <input
                    v-model="form.siNumber"
                    class="w-full border-b border-gray-300"
                />
            </div>
            <div>
                <p class="font-semibold">DATE</p>
                <input
                    type="date"
                    v-model="form.date"
                    class="w-full border-b border-gray-300"
                />
            </div>
            <div>
                <p class="font-semibold">AMOUNT</p>
                <input
                    type="text"
                    v-model="form.amount"
                    class="w-full border-b border-gray-300"
                    readonly
                />
            </div>
            <div>
                <p class="font-semibold">DATE OF DELIVERY</p>
                <input
                    type="date"
                    v-model="form.deliveryDate"
                    class="w-full border-b border-gray-300"
                />
            </div>
        </div>

        <div
            class="flex flex-col space-y-4 border-b pb-4 md:flex-row md:space-y-0 md:space-x-6"
        >
            <!-- Authorizations -->
            <div class="flex-1">
                <p class="mb-2 font-semibold">AUTHORIZATIONS</p>
                <div
                    v-for="auth in authorizations"
                    :key="auth"
                    class="flex items-center space-x-2"
                >
                    <input
                        type="checkbox"
                        :id="auth"
                        :value="auth"
                        v-model="form.authorizations"
                        class="h-4 w-4"
                    />
                    <label :for="auth">{{ auth }}</label>
                </div>
            </div>

            <!-- Checklist of Requirements -->
            <div class="flex-1">
                <p class="mb-2 font-semibold">CHECKLIST OF REQUIREMENTS</p>
                <div
                    v-for="item in checklist"
                    :key="item"
                    class="flex items-center space-x-2 text-sm"
                >
                    <input
                        type="checkbox"
                        :id="item"
                        :value="item"
                        v-model="form.checklist"
                        class="h-4 w-4"
                    />
                    <label :for="item">{{ item }}</label>
                    <input
                        v-if="form.checklist.includes(item)"
                        type="file"
                        :ref="(el) => (fileInputs[item] = el)"
                        @change="handleFileUpload($event, item)"
                        class="w-64 border-b border-gray-300"
                    />
                </div>
            </div>
        </div>

        <!-- Checklist of Requirements with File Upload -->
        <!-- <div class="space-y-2 border-b pb-4">
            <p class="mb-2 font-semibold">CHECKLIST OF REQUIREMENTS</p>
            <div
                v-for="item in checklist"
                :key="item"
                class="flex items-center space-x-2 text-sm"
            >
                <input
                    type="checkbox"
                    :id="item"
                    :value="item"
                    v-model="form.checklist"
                    class="h-4 w-4"
                />
                <label :for="item">{{ item }}</label>
                <input
                    v-if="form.checklist.includes(item)"
                    type="file"
                    :ref="(el) => (fileInputs[item] = el)"
                    @change="handleFileUpload($event, item)"
                    class="w-64 border-b border-gray-300"
                />
            </div>
        </div> -->

        <!-- Submit Button -->
        <div class="text-right">
            <button
                type="submit"
                class="rounded bg-blue-600 px-6 py-2 text-white hover:bg-blue-700"
            >
                Submit All
            </button>
        </div>
    </form>
</template>
