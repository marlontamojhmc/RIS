<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { File } from 'lucide-vue-next';
import AppSidebarLayout from '@/layouts/app/AppSidebarLayout.vue';
import LocatorAppSidebarLayout from '@/layouts/locator/LocatorAppSidebarLayout.vue'
import DynamicFormRepeater from '@/components/locator/DynamicFormRepeater.vue'
import ApplicationOptionSelect from '@/components/locator/ApplicationOptionSelect.vue'
import UploadAttachment from '@/components/locator/UploadAttachment.vue'
import Alert from '@/components/locator/Alert.vue'
import { Button } from '@/components/ui/button'
import TopCard from '@/components/common/TopCard.vue'
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem } from '@/components/ui/dropdown-menu'
import { usePage } from '@inertiajs/vue3'
import { locator } from '@/routes';
//import applications from '@/routes/applications'
import { type BreadcrumbItem } from '@/types'

// 🧭 Props
const page = usePage()
const props = defineProps({
  user: Object,
  application_form_id: [String, Number],
  articleDetail: { type: Object, default: () => null },
  categories: { type: Array, default: () => [] },
  options: { type: Array, default: () => [] },
  expired_at: { type: String, default: null },
  control_number: [String, Number],
  form_number: [String, Number],
  form_title: [String],
  start_date: [String],
  price: [String, Number],
  form: { type: Object, default: () => null },
  forms: { type: Array, default: () => [] },
  approverGroupId: [String, Number],
})

// 📌 State
const selectedForm = ref<any>(null)
const isSubmitting = ref(false)
const articles = ref<any[]>([])
const uploadedFiles = ref<any[]>([])
const localPrice = ref(props.price ?? null)
const buttonVisible = ref(false)
const forApproval = ref(false)
const approverGroupId = ref(props.approverGroupId)
const applicationId = ref(props.application_form_id)

// 📌 Forms
const createForm = useForm({
  user_id: props.user?.id,
  form_name: props.form?.name ?? '',
  form_user:'',
  type:'',
  approver_group_id:'',
  form_id:'',
  
})

const approvalForm = useForm({
  application_id: props.application_form_id,
  approver_group_id: approverGroupId.value,
  status: 'pending',
})

// 🧠 Computed
const selectedDeclaredValue = computed(() => {
  if (!props.options || !createForm.application_category_option_id) return null
  return props.options.find(
    opt => Number(opt.id) === Number(createForm.application_category_option_id)
  )
})

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Locator', href: locator.url() },
  { title: 'Create Permit', href: '/' },
]

// 👂 Watchers
watch(
  () => props.articleDetail,
  (newVal) => {
    if (newVal) articles.value.push(newVal)
  }
)

// 🧭 Lifecycle
onMounted(() => {
  applicationId.value = props.application_form_id
  approvalForm.application_id = props.application_form_id
  fetchUploads()
})

// 🌐 Fetch Uploads
async function fetchUploads() {
  if (!applicationId.value) return
  try {
    const res = await fetch(`/loctr/uploads?application_form_id=${applicationId.value}`)
    const data = await res.json()
    uploadedFiles.value = data.uploads
  } catch (err) {
    console.error('Failed to fetch uploads', err)
  }
}

// 📤 Upload Handlers
function onUploaded(res: any) {
  uploadedFiles.value.push(...res.files)
}

function onUploadError(err: any) {
  console.error('Upload error:', err)
}

// 💰 Price
function handlePriceUpdated(price: number) {
  localPrice.value = price
}

// 📝 Form submission
function submit() {
  
  createForm.post('/loctr/applications', {
    onStart: () => { createForm.processing = true 
                     isSubmitting.value =true
    },
    onSuccess: () => { buttonVisible.value = false },
    onError: () => { buttonVisible.value = true },
    onFinish: () => { createForm.processing = false 
                    forApproval.value = true},
  })
}

// 🚀 Submit for Approval
function handleApply(applicationId: string | number) {
  approvalForm.application_id = applicationId
  approvalForm.post('/approval', {
    onSuccess: () => console.log('✅ Application sent for approval'),
    onError: (errors) => console.error('❌ Approval failed', errors),
  })
}
const [ato] = page.props.applications;
const stat = ato == null ? '' : ato.status
// 📑 Form Type Selection
function selectForm(f: any) {
  //console.log(f.id);
  createForm.form_id = f.id
  createForm.form_name = f.name
  createForm.form_user =f.form_user
  createForm.type = f.form_type
  createForm.approver_group_id = f.approver_group_id
  approvalForm.approver_group_id = f.approver_group_id
  buttonVisible.value = true
}
</script>

<template>
  <LocatorAppSidebarLayout :breadcrumbs="breadcrumbs">
   
  <TopCard :stats="stat" />
     
    
    <div class="p-6 w-full mx-6 bg-white shadow-xl rounded-xl border border-gray-100 dark:bg-gray-900 dark:border-gray-700">
    
      <!-- 🏷 Title -->
      <h1 class="text-3xl font-extrabold mb-6 border-b pb-2 text-gray-900 dark:text-gray-100 dark:border-gray-700">
        {{ props.form_title ? `Applying for ${props.form_title}` : ' Application Forms' }}
      </h1>

      <!-- Alerts & Info -->
      <div v-if="props.application_form_id" class="text-left space-y-1 mb-4">
        <Alert message="You can now list Declared Article Detail" type="success" :duration="10000"/>
        <!--Alert message="You can now choose your Declared value and Validity Period" type="success" :duration="10000"/-->
        <Alert v-if="selectedDeclaredValue" message="You can now Upload Supporting Documents" type="info" :duration="10000"/>
        <Alert v-if="uploadedFiles.length" message="You can now Submit Application for Approval." type="success" :duration="10000"/>
        <p v-if="props.form_number" class="text-sm text-gray-500">
          <b>Applicant:</b> {{ props.user?.name }}
        </p>
        <p v-if="props.form_number" class="text-sm text-gray-500">
          <b>Form Number:</b> {{ props.form_number }}
        </p>
        <p v-if="props.control_number" class="text-sm text-gray-500">
          <b>Control Number:</b> {{ props.control_number }}
        </p>
        <p v-if="selectedDeclaredValue" class="text-sm text-gray-500 leading-5">
          <b>Declared Value:</b> {{ selectedDeclaredValue.name || selectedDeclaredValue.value }}<br>
          <b>Validity:</b> {{ selectedDeclaredValue.validity }}
        </p>
        <p v-if="localPrice" class="text-sm text-gray-500">
          <b>Amount:</b> ₱{{ props.price }}
        </p>
        <p v-if="props.start_date" class="text-sm text-gray-500">
          <b>Start Date:</b> {{ new Date(props.start_date).toLocaleDateString() }}
        </p>
        <p v-if="props.expired_at" class="text-sm text-gray-500">
          <b>Expires On:</b> {{ new Date(props.expired_at).toLocaleDateString() }}
        </p>
      </div>
<div v-if="props.application_form_id" class="mt-8 mb-5">
          <DynamicFormRepeater
            :formId="props.application_form_id"
            v-model="articles"
            :title="`Adding Article Details`"
          />
        </div>
      <!-- 📝 Create Form -->
      <form @submit.prevent="submit" class="space-y-6">

        <!-- Creator -->
        
<div class="flex flex-wrap gap-2">
    <button
      v-for="f in props.form"
      :key="f.id"
      @click="selectForm(f)"
      :disabled="isSubmitting"
      :class="selectedForm?.id === f.id ? 'bg-blue-600 text-white hidden' : 'bg-gray-200 text-gray-800 py-5 px-5'"
      class="px-4 py-2 rounded-md hover:bg-blue-500 hover:text-white transition"
    ><File />
      {{ f.name }}
    </button>
  </div>

        <!-- Declared Value -->
        <div v-if="props.application_form_id">
          <ApplicationOptionSelect
            v-model="createForm.application_category_option_id"
            :options="props.options"
            :application-id="props.application_form_id"
            @price-updated="handlePriceUpdated"
          />
        </div>
        <div v-if="createForm.errors.application_category_option_id" class="text-red-500 text-sm mt-1">
          {{ createForm.errors.application_category_option_id }}
        </div>

        <!-- Upload Attachments -->
        <div v-if="props.application_form_id && props.expired_at" class="space-y-4">
          <label class="block text-sm font-medium mb-1">Upload Attachments</label>
          <UploadAttachment
            :application-form-id="props.application_form_id"
            upload-url="/loctr/uploads"
            accept="image/*"
            multiple
            @uploaded="onUploaded"
            @error="onUploadError"
          />
          <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2">
            <div v-for="file in uploadedFiles" :key="file.id" class="rounded p-2 bg-gray-50 dark:bg-gray-800">
              <a :href="file.url" target="_blank" class="text-blue-600 hover:underline">
                {{ file.file_name }}
              </a>
            </div>
          </div>
        </div>

        <!-- Article Details -->
        

        <!-- Action Buttons -->
        <div class="pt-4 flex justify-center space-x-4">
          <!-- <Button 
          v-if="buttonVisible" 
          type="submit" 
          class="px-5 py-2 bg-blue-500 hover:bg-gray-300"
          >
            Generate Form-ID
          </Button> -->

          <Button
            v-if="forApproval"
            type="button"
            class="px-5 py-2 bg-blue-500 text-white hover:bg-blue-600"
            @click="handleApply(props.application_form_id)"
            :disabled="createForm.processing"
          >
            {{ createForm.processing ? 'Saving...' : 'Submit for Approval' }}
          </Button>
        </div>
      </form>
      
    </div>
  </LocatorAppSidebarLayout>
</template>

