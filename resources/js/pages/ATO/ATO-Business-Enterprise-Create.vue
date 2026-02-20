<script setup>
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();
// Props
const props = defineProps({
    application_id: { type: String, required: true },
    approver_group_id: { type: String, required: true },
    application_form_number: { type: String, required: true },
});

// Reactive form
const form = useForm({
    application_id: props.application_id,
    approver_group_id: props.approver_group_id,
    application_form_number: props.application_form_number,

    Enterprise: '',
    Sector: '',
    applicationType: '',
    leased_area_id: null,
    leased_area_label: '',
    price: '',

    businessStructure: '',
    natureOfContract: '',
    businessProfile: {
        businessName: '',
        parentCompany: '',
        taxpayerName: '',
        TIN: '',
    },
    pcic: {
        PCICPrimary: '',
        primaryLine: '',
        PCICSecondary: '',
        secondaryLine: '',
        emailPrimary: '',
        emailSecondary: '',
        location: '',
        officeAddress: '',
        contactPerson: '',
        contactNumber: '',
    },
    files: [
        { title: 'Letter of Intent', file: null },
        { title: 'Company Profile', file: null },
        { title: 'Valid Lease Contract', file: null },
        { title: 'OBO Clearance', file: null },
        { title: 'BIR Certificate of Registration', file: null },
    ],
});

// Options from API
const options = ref({
    Enterprise: [],
    Sector: [],
    Pricing: [],
});
const loading = ref(false);

// Fetch options from API
const fetchOptions = async () => {
    loading.value = true;
    try {
        const { data } = await axios.get('/api/ATO/options');
        options.value = data;
    } catch (error) {
        console.error(
            'Fetch error:',
            error.response?.data?.message || error.message,
        );
    } finally {
        loading.value = false;
    }
};

onMounted(fetchOptions);

// Compute leased-area options based on pricing & application type
const leasedAreas = computed(() => {
    if (!form.applicationType) return [];
    return options.value.pricing
        .filter((p) => p.application_type === form.applicationType)
        .map((p) => ({
            id: p.id,
            min_area: p.min_area,
            max_area: p.max_area,
            label: p.max_area
                ? `${p.min_area}–${p.max_area} sqm`
                : `${p.min_area} sqm and above`,
            price: p.price,
        }));
});
console.log(options.value.pricing);

// Watch leased area selection and update price automatically
watch(
    () => form.leased_area_id,
    (id) => {
        const selected = leasedAreas.value.find((l) => l.id === id);
        if (selected) {
            form.price = selected.price;
            form.leased_area_label = selected.label;
        } else {
            form.price = '';
            form.leased_area_label = '';
        }
    },
);

// Watch application type changes to reset leased area & price
watch(
    () => form.applicationType,
    () => {
        form.leased_area_id = null;
        form.price = '';
        form.leased_area_label = '';
    },
);

// File upload handlers
function handleFileChange(index, event) {
    form.files[index].file = event.target.files?.[0] ?? null;
}

// Submit
function submitForm() {
    form.post('/ATO', {
        onStart: () => (loading.value = true),
        onProgress: (e) => console.log('Upload progress:', e.percent),
        onFinish: () => (loading.value = false),
        onSuccess: () => {
            toast.success(
                `${props.application_form_number} Submitted Successsfully`,
            );
            form.reset();
            form.files.forEach((f) => (f.file = null));
        },
    });
}
</script>

<template>
    <AppLayout>
        <div class="space-y-8 p-6">
            <h3 class="mb-6 text-center text-xl font-bold">
                Business Enterprise- Primary/Secondary
            </h3>
            <h3 class="mb-6 text-center text-xl font-bold">
                Authority to Operate (ATO) Application Form
            </h3>

            <div v-if="!loading" class="space-y-4">
                <!-- Enterprise -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Enterprise -->
                    <div>
                        <Label>Business Enterprise Classification</Label>
                        <select
                            v-model="form.Enterprise"
                            class="w-full rounded border p-2"
                        >
                            <option disabled value="">Select Enterprise</option>
                            <option
                                v-for="item in options.Enterprise"
                                :key="item.id"
                                :value="item.id"
                            >
                                {{ item.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Sector -->
                    <div>
                        <Label>Business Sector Classification</Label>
                        <select
                            v-model="form.Sector"
                            class="w-full rounded border p-2"
                        >
                            <option disabled value="">Select Sector</option>
                            <option
                                v-for="sector in options.Sector"
                                :key="sector.id"
                                :value="sector.id"
                            >
                                {{ sector.name }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Application Type -->
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <!-- Type of Application -->
                    <div>
                        <Label>Type of Application</Label>
                        <select
                            v-model="form.applicationType"
                            class="w-full rounded border p-2"
                        >
                            <option disabled value="">Select Type</option>
                            <option value="New">New</option>
                            <option value="Renewal">Renewal</option>
                        </select>
                    </div>

                    <!-- Leased Area -->
                    <div>
                        <Label>Leased Area</Label>
                        <select
                            v-model="form.leased_area_id"
                            class="w-full rounded border p-2"
                        >
                            <option disabled value="">
                                Select Leased Area
                            </option>
                            <option
                                v-for="area in leasedAreas"
                                :key="area.id"
                                :value="area.id"
                            >
                                {{ area.label }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Price (readonly) -->
                <div>
                    <Label>Application Fee:</Label>
                    <input
                        type="text"
                        class="w-full rounded border bg-gray-100 p-2"
                        :value="`₱ ${form.price}`"
                        readonly
                    />
                </div>
            </div>

            <!-- Business Structure -->
            <div class="space-y-2">
                <Label>Business Structure</Label>
                <div class="mt-1 grid grid-cols-2 gap-2 md:grid-cols-4">
                    <label class="flex items-center gap-2"
                        ><input
                            type="radio"
                            value="Sole Proprietor"
                            v-model="form.businessStructure"
                        />Sole Proprietor</label
                    >
                    <label class="flex items-center gap-2"
                        ><input
                            type="radio"
                            value="Partnership"
                            v-model="form.businessStructure"
                        />Partnership</label
                    >
                    <label class="flex items-center gap-2"
                        ><input
                            type="radio"
                            value="Corporation"
                            v-model="form.businessStructure"
                        />Corporation</label
                    >
                    <label class="flex items-center gap-2"
                        ><input
                            type="radio"
                            value="Cooperative"
                            v-model="form.businessStructure"
                        />Cooperative</label
                    >
                </div>
            </div>

            <!-- File uploads -->
            <div class="space-y-4">
                <h2 class="text-lg font-bold">Upload Supporting Documents</h2>
                <div
                    v-for="(row, index) in form.files"
                    :key="index"
                    class="grid grid-cols-1 items-center gap-3 rounded bg-gray-50 p-4 md:grid-cols-3"
                >
                    <div class="rounded border bg-gray-100 p-2 font-semibold">
                        {{ row.title }}
                    </div>
                    <Input
                        type="file"
                        class="w-full"
                        @change="(e) => handleFileChange(index, e)"
                    />
                </div>
            </div>

            <!-- Business Profile -->
            <div>
                <h2 class="mb-3 text-lg font-bold">
                    Business Enterprise Profile
                </h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <Input
                        v-model="form.businessProfile.businessName"
                        placeholder="Trade's Name"
                    />
                    <Input
                        v-model="form.businessProfile.parentCompany"
                        placeholder="Name of Parent Company"
                    />
                    <Input
                        v-model="form.businessProfile.taxpayerName"
                        placeholder="Taxpayer's Name"
                    />
                    <Input
                        v-model="form.businessProfile.TIN"
                        placeholder="TIN"
                    />

                    <!-- Nature of Contract -->
                    <div class="md:col-span-2">
                        <Label>Nature of Contract</Label>
                        <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-2">
                            <label class="flex items-center gap-2"
                                ><input
                                    type="radio"
                                    value="Direct Lease with BCDA/JHMC"
                                    v-model="form.natureOfContract"
                                />Direct Lease with BCDA/JHMC</label
                            >
                            <label class="flex items-center gap-2"
                                ><input
                                    type="radio"
                                    value="Sub Leasee with Principal Locator"
                                    v-model="form.natureOfContract"
                                />Sub Leasee with Principal Locator</label
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- PCIC -->
            <div>
                <h2 class="mb-3 text-lg font-bold">PCIC Information</h2>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <Input
                        v-model="form.pcic.PCICPrimary"
                        placeholder="PCIC Primary"
                    />
                    <Input
                        v-model="form.pcic.primaryLine"
                        placeholder="Line of Business"
                    />
                    <Input
                        v-model="form.pcic.PCICSecondary"
                        placeholder="PCIC Secondary"
                    />
                    <Input
                        v-model="form.pcic.secondaryLine"
                        placeholder="Secondary Line of Business"
                    />
                    <Input
                        v-model="form.pcic.emailPrimary"
                        type="email"
                        placeholder="Primary Email Address"
                    />
                    <Input
                        v-model="form.pcic.emailSecondary"
                        type="email"
                        placeholder="Secondary Email Address"
                    />
                    <Input
                        v-model="form.pcic.location"
                        placeholder="Location within JHMC"
                    />
                    <Input
                        v-model="form.pcic.officeAddress"
                        placeholder="Main Office Address"
                    />
                    <Input
                        v-model="form.pcic.contactPerson"
                        placeholder="Contact Person"
                    />
                    <Input
                        v-model="form.pcic.contactNumber"
                        placeholder="Contact Number"
                    />
                </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <Button
                    class="rounded bg-green-600 px-6 py-2 text-white"
                    @click="submitForm"
                    >Submit</Button
                >
                <Button
                    class="rounded bg-red-600 px-6 py-2 text-white"
                    @click="form.reset()"
                    >Clear</Button
                >
            </div>
        </div>
    </AppLayout>
</template>
