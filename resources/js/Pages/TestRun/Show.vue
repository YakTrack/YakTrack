<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Test Runs', url: route('test-run.index')},
                    {title: testRun.name},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ testRun.name }}</template>
        <template #top-right-toolbar>
            <button-link :href="route('test-run.index')" color="gray">
                <i class="fa fa-arrow-left text-gray-100 mr-2"></i>
                Back to List
            </button-link>
        </template>

        <!-- Test Run Info -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                    <span>{{ testRun.project.name }}</span>
                    <span>{{ formatDate(testRun.executed_at) }}</span>
                    <span>by {{ testRun.executed_by_user.name }}</span>
                </div>
                <div v-if="testRun.description" class="mt-3">
                    <p class="text-gray-700 dark:text-gray-300">{{ testRun.description }}</p>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <span class="text-gray-600 dark:text-gray-300 font-semibold text-sm">{{ summary.total }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tests</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm">{{ summary.pending }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <span class="text-green-600 dark:text-green-400 font-semibold text-sm">{{ summary.passed }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Passed</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                <span class="text-red-600 dark:text-red-400 font-semibold text-sm">{{ summary.failed }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Failed</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center">
                                <span class="text-yellow-600 dark:text-yellow-400 font-semibold text-sm">{{ summary.skipped + summary.blocked }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Skipped/Blocked</p>
                        </div>
                    </div>
                </div>
            </div>
    </div>

        <!-- Description -->
        <div v-if="testRun.description" class="card mb-6">
            <div class="card-header">
                <h2 class="card-title">Description</h2>
            </div>
            <div class="card-body">
                <p class="text-gray-600 dark:text-gray-300 whitespace-pre-wrap">{{ testRun.description }}</p>
            </div>
        </div>

        <!-- Test Results -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Test Results</h2>
            </div>
            <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Criteria
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Notes
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Evidence
              </th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="result in testRun.test_results" :key="result.id" :class="getStatusRowClass(result.status)">
              <td class="px-6 py-4">
                <div>
                  <div class="flex items-center">
                    <div v-if="result.acceptance_criteria.code" class="text-base font-medium text-gray-900 dark:text-gray-400 font-mono mr-2">
                      {{ result.acceptance_criteria.code }}
                    </div>
                    <div class="text-sm text-gray-600 dark:text-white">
                      {{ result.acceptance_criteria.name }}
                    </div>
                  </div>
                  <div v-if="result.acceptance_criteria.description" class="mt-1">
                    <GherkinText :text="result.acceptance_criteria.description" size="xs" />
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select
                  :value="result.status"
                  @change="updateStatus(result, $event.target.value)"
                  class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md"
                >
                  <option value="pending">Pending</option>
                  <option value="passed">Passed</option>
                  <option value="failed">Failed</option>
                  <option value="skipped">Skipped</option>
                  <option value="blocked">Blocked</option>
                </select>
              </td>
              <td class="px-6 py-4">
                <div v-if="editingNotesId !== result.id">
                  <div class="flex items-start justify-between gap-2">
                    <div class="flex-1">
                      <p v-if="result.notes" class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">
                        {{ result.notes }}
                      </p>
                      <p v-else class="text-sm text-gray-400 dark:text-gray-500 italic">
                        No notes
                      </p>
                    </div>
                    <button
                      @click="startEditingNotes(result)"
                      class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm flex-shrink-0"
                      title="Edit notes"
                    >
                      <i class="fa fa-edit"></i>
                    </button>
                  </div>
                </div>
                <div v-else class="space-y-2">
                  <textarea
                    v-model="editingNotes"
                    placeholder="Add notes..."
                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md w-full"
                    rows="3"
                    ref="notesTextarea"
                  ></textarea>
                  <div class="flex gap-2">
                    <button
                      @click="saveNotes(result)"
                      class="px-3 py-1 text-xs bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    >
                      Save
                    </button>
                    <button
                      @click="cancelEditingNotes"
                      class="px-3 py-1 text-xs bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
                    >
                      Cancel
                    </button>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-2">
                  <div
                    v-for="evidence in result.evidence"
                    :key="evidence.id"
                    class="flex items-center"
                  >
                    <!-- Image Evidence - Show Thumbnail -->
                    <div v-if="evidence.type === 'image' && evidence.file_url" class="cursor-pointer" @click="viewImage(evidence.file_url)">
                      <img
                        :src="evidence.file_url"
                        :alt="'Evidence image ' + evidence.id"
                        class="w-12 h-12 object-cover rounded border border-gray-300 dark:border-gray-600 hover:opacity-80 hover:ring-2 hover:ring-indigo-500 transition-all"
                        @error="handleImageError"
                        title="Click to view full size"
                      />
                    </div>
                    <!-- Image Evidence - Fallback if no URL -->
                    <span v-else-if="evidence.type === 'image'" class="text-xs text-blue-600 dark:text-blue-400">
                      📷
                    </span>
                    <!-- Text Evidence -->
                    <span v-else class="text-xs text-green-600 dark:text-green-400" title="Text note">
                      📝
                    </span>
                  </div>
                  <span v-if="result.evidence.length === 0" class="text-xs text-gray-400 italic">
                    No evidence
                  </span>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button
                  @click="showEvidenceModal(result)"
                  class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300"
                >
                  Manage Evidence
                </button>
              </td>
            </tr>
          </tbody>
        </table>
            </div>
            <div v-if="availableCriteria && availableCriteria.length > 0" class="card-body border-t border-gray-200 dark:border-gray-700 pt-4">
                <button
                    @click="openAddCriteriaModal"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                >
                    <i class="fa fa-plus mr-2"></i>
                    Add Criteria
                </button>
            </div>
        </div>

    <!-- Evidence Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Evidence for {{ selectedResult?.acceptance_criteria.name }}
          </h3>
          
          <!-- Evidence List -->
          <div class="space-y-2 mb-4 max-h-96 overflow-y-auto">
            <div
              v-for="evidence in selectedResult?.evidence"
              :key="evidence.id"
              class="p-2 bg-gray-50 dark:bg-gray-700 rounded"
            >
              <div class="flex items-start justify-between gap-2">
                <div class="flex-1">
                  <!-- Image Evidence -->
                  <div v-if="evidence.type === 'image'" class="space-y-2">
                    <div class="flex items-center space-x-2 mb-2">
                      <span class="text-blue-600 dark:text-blue-400">📷</span>
                      <span class="text-sm font-medium text-gray-900 dark:text-white">Image</span>
                    </div>
                    <div v-if="evidence.file_url" class="cursor-pointer" @click="viewImage(evidence.file_url)">
                      <img
                        :src="evidence.file_url"
                        :alt="'Evidence image ' + evidence.id"
                        class="max-w-full h-auto max-h-32 rounded border border-gray-300 dark:border-gray-600 hover:opacity-80 transition-opacity"
                        @error="handleImageError"
                      />
                      <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Click to view full size</p>
                    </div>
                    <div v-else class="text-sm text-gray-500 dark:text-gray-400">
                      Image URL not available
                    </div>
                  </div>
                  
                  <!-- Text Evidence -->
                  <div v-else class="space-y-2">
                    <div class="flex items-center space-x-2 mb-2">
                      <span class="text-green-600 dark:text-green-400">📝</span>
                      <span class="text-sm font-medium text-gray-900 dark:text-white">Text Note</span>
                    </div>
                    <p class="text-sm text-gray-900 dark:text-white whitespace-pre-wrap">{{ evidence.content }}</p>
                  </div>
                </div>
                <button
                  @click="removeEvidence(evidence)"
                  class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm flex-shrink-0 ml-2"
                  title="Remove evidence"
                >
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
            <div v-if="selectedResult?.evidence.length === 0" class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">
              No evidence added yet
            </div>
          </div>

          <!-- Add Evidence Form -->
          <form @submit.prevent="addEvidence" class="space-y-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Evidence Type
              </label>
              <select
                v-model="evidenceForm.type"
                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md"
              >
                <option value="image">Image</option>
                <option value="text">Text Note</option>
              </select>
            </div>
            
            <div v-if="evidenceForm.type === 'image'">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Upload Image
              </label>
              <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md"
              />
            </div>
            
            <div v-if="evidenceForm.type === 'text'">
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Text Content
              </label>
              <textarea
                v-model="evidenceForm.content"
                rows="3"
                placeholder="Enter text evidence..."
                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md"
              ></textarea>
            </div>

            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="closeModal"
                class="px-3 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Add Evidence
              </button>
            </div>
          </form>
        </div>
      </div>
        </div>

    <!-- Image Viewer Modal -->
    <div v-if="imageViewerUrl" class="fixed inset-0 bg-black bg-opacity-75 z-[60] flex items-center justify-center p-4" @click="closeImageViewer">
      <div class="relative max-w-7xl max-h-full">
        <button
          @click="closeImageViewer"
          class="absolute top-4 right-4 text-white hover:text-gray-300 text-2xl font-bold bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center"
          title="Close"
        >
          ×
        </button>
        <img
          :src="imageViewerUrl"
          alt="Evidence image"
          class="max-w-full max-h-[90vh] rounded shadow-2xl"
          @click.stop
        />
      </div>
    </div>

    <!-- Add Criteria Modal -->
    <div v-if="showAddCriteriaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Add Acceptance Criteria to Test Run
          </h3>
          
          <form @submit.prevent="addCriteria" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Acceptance Criteria <span class="text-red-500">*</span>
              </label>
              <select
                v-model="criteriaForm.acceptance_criteria_id"
                required
                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md"
              >
                <option value="">Select an acceptance criteria...</option>
                <option
                  v-for="criterion in availableCriteria"
                  :key="criterion.id"
                  :value="criterion.id"
                >
                  {{ criterion.code ? criterion.code + ' - ' : '' }}{{ criterion.name }}
                </option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Initial Status
              </label>
              <select
                v-model="criteriaForm.status"
                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md"
              >
                <option value="pending">Pending</option>
                <option value="passed">Passed</option>
                <option value="failed">Failed</option>
                <option value="skipped">Skipped</option>
                <option value="blocked">Blocked</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Notes (Optional)
              </label>
              <textarea
                v-model="criteriaForm.notes"
                rows="3"
                placeholder="Add any initial notes..."
                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md"
              ></textarea>
            </div>

            <div class="flex justify-end space-x-2">
              <button
                type="button"
                @click="closeAddCriteriaModal"
                class="px-3 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Cancel
              </button>
              <button
                type="submit"
                class="px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
              >
                Add Criteria
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    </layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { reactive, ref, nextTick } from 'vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'
import GherkinText from '@/Shared/GherkinText.vue'

const props = defineProps({
  testRun: Object,
  summary: Object,
  availableCriteria: {
    type: Array,
    default: () => [],
  },
})

const showModal = ref(false)
const selectedResult = ref(null)
const fileInput = ref(null)
const editingNotesId = ref(null)
const editingNotes = ref('')
const notesTextarea = ref(null)
const imageViewerUrl = ref(null)
const showAddCriteriaModal = ref(false)

const evidenceForm = reactive({
  type: 'image',
  content: '',
})

const criteriaForm = reactive({
  acceptance_criteria_id: '',
  status: 'pending',
  notes: '',
})

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const getStatusRowClass = (status) => {
  const classes = {
    pending: 'bg-blue-50 dark:bg-blue-900/20',
    passed: 'bg-green-50 dark:bg-green-900/20',
    failed: 'bg-red-50 dark:bg-red-900/20',
    skipped: 'bg-yellow-50 dark:bg-yellow-900/20',
    blocked: 'bg-gray-50 dark:bg-gray-700/50',
  }
  return classes[status] || ''
}

const updateStatus = (result, status) => {
  router.patch(route('test-result.update', result.id), { status }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const startEditingNotes = (result) => {
  editingNotesId.value = result.id
  editingNotes.value = result.notes || ''
  // Focus the textarea after it's rendered
  nextTick(() => {
    if (notesTextarea.value) {
      notesTextarea.value.focus()
    }
  })
}

const cancelEditingNotes = () => {
  editingNotesId.value = null
  editingNotes.value = ''
}

const saveNotes = (result) => {
  router.patch(route('test-result.update', result.id), { notes: editingNotes.value }, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      editingNotesId.value = null
      editingNotes.value = ''
    },
  })
}

const showEvidenceModal = (result) => {
  selectedResult.value = result
  showModal.value = true
  evidenceForm.type = 'image'
  evidenceForm.content = ''
}

const closeModal = () => {
  showModal.value = false
  selectedResult.value = null
  evidenceForm.type = 'image'
  evidenceForm.content = ''
}

const addEvidence = () => {
  const formData = new FormData()
  formData.append('type', evidenceForm.type)
  
  if (evidenceForm.type === 'image' && fileInput.value.files[0]) {
    formData.append('file', fileInput.value.files[0])
  } else if (evidenceForm.type === 'text') {
    formData.append('content', evidenceForm.content)
  }

  router.post(route('test-result.evidence.store', selectedResult.value.id), formData, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      closeModal()
    },
  })
}

const removeEvidence = (evidence) => {
  router.delete(route('test-result.evidence.destroy', evidence.id), {
    preserveState: true,
    preserveScroll: true,
  })
}

const viewImage = (url) => {
  imageViewerUrl.value = url
}

const closeImageViewer = () => {
  imageViewerUrl.value = null
}

const handleImageError = (event) => {
  event.target.style.display = 'none'
  const parent = event.target.parentElement
  if (parent) {
    const errorMsg = document.createElement('p')
    errorMsg.className = 'text-sm text-red-600 dark:text-red-400'
    errorMsg.textContent = 'Failed to load image'
    parent.appendChild(errorMsg)
  }
}

const openAddCriteriaModal = () => {
  showAddCriteriaModal.value = true
  criteriaForm.acceptance_criteria_id = ''
  criteriaForm.status = 'pending'
  criteriaForm.notes = ''
}

const closeAddCriteriaModal = () => {
  showAddCriteriaModal.value = false
  criteriaForm.acceptance_criteria_id = ''
  criteriaForm.status = 'pending'
  criteriaForm.notes = ''
}

const addCriteria = () => {
  router.post(route('test-run.test-result.store', props.testRun.id), criteriaForm, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      closeAddCriteriaModal()
    },
  })
}
</script>


