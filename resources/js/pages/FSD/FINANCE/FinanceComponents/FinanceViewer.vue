<script setup>
const props = defineProps({
  application: {
    type: Object,
    required: true
  },
  approver:{
    type:Object,
    required: true,
  },
  group:{
    type:Object,
    required:true,
  }
})
</script>

<template>
  <div class="bg-white rounded-xl shadow p-6 space-y-8">

    <!-- Application Basic Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-b pb-4">
      <!-- Left column: Form Title, Form No, Status -->
      <div class="space-y-2">
        <h3 class="text-lg bg-gray-500 rounded text-white text-center font-semibold mb-2">
          Application Basic Information
        </h3>
        <h2 class="text-md font-bold text-gray-900">
          {{ props.application.form_title }}
        </h2>
        <p class="text-sm text-gray-600">
          Form No: <span class="font-semibold">{{ props.application.form_number }}</span>
        </p>
        <p class="text-sm text-gray-600">
          Status: 
          <span class="font-semibold" :class="props.application.status === 'Pending' ? 'text-yellow-600' : 'text-green-600'">
            {{ props.application.status }}
          </span>
        </p>
      </div>

      <!-- Right column: Declared Value & Validity -->
      <div v-if="props.application.options?.length" class="border rounded bg-gray-50">
        <h3 class="text-lg bg-gray-500 rounded text-white text-center font-semibold mb-4">
          Declared Value and Validity
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
          <p><strong>Selected Value:</strong> {{ props.application.options[0].name }}</p>
          <p><strong>Validity:</strong> {{ props.application.options[0].validity }}</p>
           <p><strong>Amount:</strong>₱{{ props.application.selections[0].amount }}</p>
        </div>
      </div>
    </div>

    <!-- Article Details Table -->
    <div>
      <h3 class="text-lg bg-gray-500 text-white text-center font-semibold mb-2">
        Article Details
      </h3>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-200 rounded-lg">
          <thead class="bg-gray-100">
            <tr class="text-left">
              <th class="px-4 py-2 border-b">Marks & Number</th>
              <th class="px-4 py-2 border-b">Quantity</th>
              <th class="px-4 py-2 border-b">Description</th>
              <th class="px-4 py-2 border-b">Gross Weight</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="detail in props.application.article_details" :key="detail.id" class="bg-white hover:bg-gray-50">
              <td class="px-4 py-2 border-b">{{ detail.marks_and_number }}</td>
              <td class="px-4 py-2 border-b">{{ detail.qty }}</td>
              <td class="px-4 py-2 border-b">{{ detail.detailed_description_of_article }}</td>
              <td class="px-4 py-2 border-b">{{ detail.gross_weight }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Uploads -->
    <div v-if="props.application.uploads?.length">
      <h3 class="text-lg bg-gray-500 text-white text-center font-semibold mb-2">
        Supporting Document'/s
      </h3>
      <div v-for="file in props.application.uploads" :key="file.id" class="flex items-center gap-4 border rounded-lg p-3 bg-gray-50">
        <a :href="`/storage/${file.file_path}`" target="_blank" class="flex items-center gap-4">
          <img 
            v-if="file.file_type.includes('image')" 
            :src="`/storage/${file.file_path}`"
            alt="Upload Preview"
            class="w-20 h-20 object-cover rounded-lg border"
          />
          <div>
            <p class="font-medium underline text-blue-600 hover:text-blue-800">{{ file.file_name }}</p>
            <p class="text-xs text-gray-500">{{ (file.file_size / 1024).toFixed(1) }} KB</p>
          </div>
        </a>
      </div>
    </div>

    <!-- Approval -->
    <div>
      <h3 class="text-lg bg-gray-500 text-white text-center font-semibold mb-2">
        Approval Info
      </h3>
      <div class="border rounded-lg p-4 bg-gray-50">
        <p><strong>Status:</strong> {{ props.application.approval.status }}</p>
        <p class="py-1"><strong>Approver Group:</strong> {{ props.group.name }}</p>
        <p v-if="props.application.approval.remark"><strong>Remark:</strong> {{ props.application.approval.remark }}</p>
      </div>
    </div>
     <!--Payment Status and Invoice number-->
     <div>
      <h3 class="text-lg bg-gray-500 text-white text-center font-semibold mb-2">
        Payment Status and Invoice Number
      </h3>
      <div class="border rounded-lg p-4 bg-gray-50">
       <p><strong>Payment Status:</strong>
    {{ props.application.approval?.payment_status ? props.application.approval.payment_status : 'Not yet available' }}
</p>

<p class="py-1"><strong>ISNUMBER:</strong>
    {{ props.application.approval?.IS_Number ? props.application.approval.IS_Number : 'Not yet available' }}
</p>
      </div>
    </div>
  </div>
</template>
