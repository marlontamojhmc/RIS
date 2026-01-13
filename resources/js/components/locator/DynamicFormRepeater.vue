<script setup lang="ts">
import { ref, watch } from 'vue'
import axios from 'axios'
import { Plus, X, Trash2, Save, FilePenLine, ShieldX } from 'lucide-vue-next'

const props = defineProps({
  modelValue: { type: Array, default: () => [] },
  title: { type: String, default: 'Submit New Shipping Article' },
  formId: { type: [String, Number], required: true }
})

const emit = defineEmits(['update:modelValue'])

const savedArticles = ref([...props.modelValue])
const isOpen = ref(false)
const form = ref({
  application_form_id: props.formId,
  marks_and_number: '',
  qty: null,
  detailed_description_of_article: '',
  gross_weight: ''
})

const editingId = ref<number | null>(null)
const editableRow = ref({
  marks_and_number: '',
  qty: null as number | null,
  detailed_description_of_article: '',
  gross_weight: ''
})

const openModal = () => {
  form.value = {
    application_form_id: props.formId,
    marks_and_number: '',
    qty: null,
    detailed_description_of_article: '',
    gross_weight: ''
  }
  isOpen.value = true
}
const closeModal = () => { isOpen.value = false }

const submitArticle = async () => {
  try {
    const res = await axios.post('/loctr/articles', form.value)
    const newArticle = res.data.article
    if (newArticle) {
      savedArticles.value.push(newArticle)
      emit('update:modelValue', savedArticles.value)
    }
    closeModal()
  } catch (err) {
    console.error(err.response?.data?.errors || err)
    alert('Failed to save article')
  }
}

const removeArticle = async (id: number) => {
  const index = savedArticles.value.findIndex(a => a.id === id)
  if (index === -1) return

  const [deletedArticle] = savedArticles.value.splice(index, 1)
  emit('update:modelValue', savedArticles.value)

  try {
    await axios.delete(`/loctr/articles/${id}`)
  } catch (err) {
    savedArticles.value.splice(index, 0, deletedArticle)
    emit('update:modelValue', savedArticles.value)
    alert('Failed to delete article')
  }
}

const startEdit = (article: any) => {
  editingId.value = article.id
  editableRow.value = {
    marks_and_number: article.marks_and_number,
    qty: article.qty,
    detailed_description_of_article: article.detailed_description_of_article,
    gross_weight: article.gross_weight
  }
}
const cancelEdit = () => { editingId.value = null }

const updateArticle = async (id: number) => {
  try {
    const original = savedArticles.value.find(a => a.id === id)
    if (!original) return

    const changedData: any = {}
    for (const key in editableRow.value) {
      if (editableRow.value[key] !== original[key]) {
        changedData[key] = editableRow.value[key]
      }
    }

    if (Object.keys(changedData).length === 0) {
      editingId.value = null
      return
    }

    const res = await axios.put(`/loctr/articles/${id}`, changedData)
    const updatedArticle = res.data.article || res.data

    if (updatedArticle) {
      const index = savedArticles.value.findIndex(a => a.id === id)
      if (index !== -1) savedArticles.value[index] = { ...original, ...updatedArticle }
      emit('update:modelValue', savedArticles.value)
    }

    editingId.value = null
  } catch (err) {
    console.error(err.response?.data?.errors || err)
    alert('Failed to update article')
  }
}

watch(() => props.modelValue, (newVal) => {
  if (JSON.stringify(newVal) !== JSON.stringify(savedArticles.value)) {
    savedArticles.value = [...newVal]
  }
}, { deep: true })
</script>

<template>
  <div class="space-y-6">
    <!-- Add Button -->
    <button
      @click="openModal"
      class="flex items-center space-x-2 px-3 py-2 bg-indigo-600 text-white font-semibold rounded-md hover:bg-indigo-700 transition"
    >
      <Plus class="w-5 h-5" />
      <span>Add Article</span>
    </button>

    <!-- Table -->
    <div class="overflow-x-auto shadow-lg rounded-xl border border-gray-200 dark:border-gray-700">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-indigo-50 dark:bg-gray-800">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase">#</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase">Marks & Number</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase">Quantity</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase">Description</th>
            <th class="px-4 py-3 text-left text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase">Weight</th>
            <th class="px-4 py-3 text-right text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
          <tr v-if="savedArticles.length === 0">
            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 italic">
              No articles yet.
            </td>
          </tr>

          <tr v-for="(article, index) in savedArticles" :key="article.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ index + 1 }}</td>

            <!-- Marks -->
            <td class="px-4 py-3 font-semibold text-blue-800 dark:text-blue-300">
              <span v-if="editingId !== article.id">{{ article.marks_and_number }}</span>
              <input
                v-else
                v-model="editableRow.marks_and_number"
                class="border p-1 rounded w-full bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
              />
            </td>

            <!-- Qty -->
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
              <span v-if="editingId !== article.id">{{ article.qty }}</span>
              <input
                v-else
                type="number"
                v-model.number="editableRow.qty"
                class="border p-1 rounded w-full bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
              />
            </td>

            <!-- Description -->
            <td class="px-4 py-3 max-w-xs truncate text-gray-900 dark:text-gray-100">
              <span v-if="editingId !== article.id">{{ article.detailed_description_of_article }}</span>
              <input
                v-else
                v-model="editableRow.detailed_description_of_article"
                class="border p-1 rounded w-full bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
              />
            </td>

            <!-- Weight -->
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">
              <span v-if="editingId !== article.id">{{ article.gross_weight || '—' }}</span>
              <input
                v-else
                v-model="editableRow.gross_weight"
                class="border p-1 rounded w-full bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600"
              />
            </td>

            <!-- Actions -->
            <td class="px-4 py-3 text-right flex justify-end space-x-1">
              <button v-if="editingId !== article.id" @click="startEdit(article)" class="p-1 rounded-full text-blue-500 hover:bg-blue-100 dark:hover:bg-gray-700">
                <FilePenLine class="w-4 h-4" />
              </button>
              <button v-else @click="updateArticle(article.id)" class="p-1 rounded-full text-green-500 hover:bg-green-100 dark:hover:bg-gray-700">
                <Save class="w-4 h-4" />
              </button>
              <button v-if="editingId === article.id" @click="cancelEdit" class="p-1 rounded-full text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
                <ShieldX class="w-4 h-4" />
              </button>
              <button type="button" @click="removeArticle(article.id)" class="p-1 rounded-full text-red-500 hover:bg-red-100 dark:hover:bg-gray-700">
                <Trash2 class="w-4 h-4" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal -->
    <div v-if="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-70" @click.self="closeModal">
      <div class="relative w-full max-w-xl max-h-[90vh] overflow-y-auto bg-white dark:bg-gray-900 rounded-xl shadow-2xl" @click.stop>
        <form @submit.prevent="submitArticle" class="p-6 space-y-6">
          <div class="flex justify-between items-center border-b dark:border-gray-700 pb-4">
            <h2 class="text-2xl font-extrabold text-gray-900 dark:text-gray-100">{{ title }}</h2>
            <button type="button" @click="closeModal" class="p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800">
              <X class="w-6 h-6" />
            </button>
          </div>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Marks and number</label>
              <input v-model="form.marks_and_number" type="text" required class="w-full border p-3 rounded-lg bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
              <input v-model.number="form.qty" type="number" min="1" required class="w-full border p-3 rounded-lg bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
              <input v-model="form.detailed_description_of_article" type="text" required class="w-full border p-3 rounded-lg bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gross weight (optional)</label>
              <input v-model="form.gross_weight" type="text" class="w-full border p-3 rounded-lg bg-white dark:bg-gray-800 dark:text-gray-100 dark:border-gray-600" />
            </div>
          </div>

          <div class="pt-4 border-t dark:border-gray-700 flex justify-end space-x-3">
            <button type="button" @click="closeModal" class="px-2 py-1 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
              Close
            </button>
            <button type="submit" class="px-2 py-1 bg-green-600 text-white rounded-lg flex items-center space-x-2 hover:bg-green-700">
              <Save class="w-4 h-4" />
              <span>Save Article</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
