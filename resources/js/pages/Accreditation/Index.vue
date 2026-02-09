<script setup>
import AppLayout from '@/layouts/AppLayout.vue'
import { FilePen } from 'lucide-vue-next';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  forms: {
    type: Array,
    required: true,
  },
  user: {
    type: Object,
    required: true,
  },
})
const createForm = useForm({
  user_id: props.user?.id,
  form_name: props.form?.name ?? '',
  form_user:props.user.details.business_type.id,
  type:props.forms.form_type,
  approver_group_id:'',
  form_id: props.forms.id,
})
const handleSelect = (form) => {
  createForm.form_id = form.id
  createForm.approver_group_id= form.approver_group_id
  createForm.form_name = form.form_type
  createForm.type = form.form_type

  // Inertia handles processing automatically
  createForm.post('/loctr/applications', {
    onSuccess: () => {
      console.log('Application created!')
    },
    onError: (errors) => {
      console.log(errors)
    },
  })
}
</script>
<template>
  <AppLayout>
    <div class="max-w-4xl mx-auto p-6">
     
        <div class="grid grid-cols-3 md:grid-cols-3 gap-4">
        <button
           v-for="form in forms"
          :key="form.id"
           @click="handleSelect(form)"
          class="w-full py-6 bg-blue-600 text-white font-semibold rounded-xl
                 hover:bg-blue-700 transition text-center"
        >
        <FilePen class="w-8 h-8"/>
          <div class="text-lg font-bold text-white text-center">
            {{ form.name }}
          </div>

          <div class="text-sm text-blue-100 mt-1">
            
          </div>
        </button>

      </div>
    </div>
</AppLayout>
</template>
