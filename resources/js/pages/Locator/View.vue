<script setup>
const props = defineProps({
    app:{
        type: Object,
        required: true,
    }
})

</script>
<template>
  <div class="p-6 space-y-6">
    <!-- Header -->
    <div class="border-b pb-4">
      <h1 class="text-2xl font-bold">{{ app.form_title }}</h1>
      <p class="text-gray-600">Status: {{ app.status }}</p>
      <p class="text-gray-600 text-sm">
        Created: {{ new Date(app.created_at).toLocaleString() }}
      </p>
    </div>

    <!-- Basic Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="p-4 border rounded-lg">
        <h2 class="font-semibold mb-2">Application Info</h2>
        <p><strong>Form Number:</strong> {{ app.form_number }}</p>
        <p><strong>User ID:</strong> {{ app.user_id }}</p>
        <p><strong>Control Number:</strong> {{ app.control_number ?? '-' }}</p>
      </div>

      <div class="p-4 border rounded-lg">
        <h2 class="font-semibold mb-2">Selection</h2>
        <div v-if="app.selections.length">
          <p><strong>Amount:</strong> {{ app.selections[0].amount }}</p>
          <p><strong>Option ID:</strong> {{ app.selections[0].option_id }}</p>
         <p><strong>Created At:</strong> {{ new Date(app.created_at).toLocaleString() }}</p>
          <p><strong>Expires At:</strong> {{ new Date(app.selections[0].Expired_at).toLocaleString() }}</p>
        </div>
        <div v-else class="text-gray-500">No selection data.</div>
      </div>
    </div>

    <!-- Article Details -->
    <div class="p-4 border rounded-lg">
      <h2 class="font-semibold mb-4">Article Details</h2>

      <table class="min-w-full border-collapse text-sm">
        <thead>
          <tr class="bg-gray-100 text-left">
            <th class="p-2 border">Marks & Number</th>
            <th class="p-2 border">Qty</th>
            <th class="p-2 border">Description</th>
            <th class="p-2 border">Gross Weight</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="item in app.article_details"
            :key="item.id"
            class="border-b"
          >
            <td class="p-2 border">{{ item.marks_and_number }}</td>
            <td class="p-2 border">{{ item.qty }}</td>
            <td class="p-2 border">{{ item.detailed_description_of_article }}</td>
            <td class="p-2 border">{{ item.gross_weight }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Uploads -->
    <div class="p-4 border rounded-lg">
  <h2 class="font-semibold mb-4">Uploaded Supporting Document/s</h2>

  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div
      v-for="file in app.uploads"
      :key="file.id"
      class="border rounded-lg p-2"
    >
    <a :href="`/storage/${file.file_path}`" target="_blank" >
      <img
        :src="`/storage/${file.file_path}`"
        class="w-full h-32 object-cover rounded"
        alt="Uploaded file"
      />

      <div class="mt-2">
        <p class="text-sm font-medium">{{ file.file_name }}</p>
        <p class="text-xs text-gray-500">Type: {{ file.file_type }}</p>
        <p class="text-xs text-gray-500">
          Size: {{ (file.file_size / 1024).toFixed(1) }} KB
        </p>
        <!-- Bind href properly -->
        
      </div>
       </a>
    </div>
   
  </div>
    </div>
</div>

</template>