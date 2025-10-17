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
        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 dark:text-blue-400 font-semibold text-sm">{{ summary.total }}</span>
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
            <tr v-for="result in testRun.test_results" :key="result.id">
              <td class="px-6 py-4">
                <div>
                  <div class="text-sm font-medium text-gray-900 dark:text-white">
                    {{ result.acceptance_criteria.name }}
                  </div>
                  <div v-if="result.acceptance_criteria.code" class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                    {{ result.acceptance_criteria.code }}
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <select
                  :value="result.status"
                  @change="updateStatus(result, $event.target.value)"
                  class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md"
                >
                  <option value="passed">Passed</option>
                  <option value="failed">Failed</option>
                  <option value="skipped">Skipped</option>
                  <option value="blocked">Blocked</option>
                </select>
              </td>
              <td class="px-6 py-4">
                <textarea
                  :value="result.notes"
                  @blur="updateNotes(result, $event.target.value)"
                  placeholder="Add notes..."
                  class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md w-full"
                  rows="2"
                ></textarea>
              </td>
              <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                  <div
                    v-for="evidence in result.evidence"
                    :key="evidence.id"
                    class="flex items-center space-x-1"
                  >
                    <span v-if="evidence.type === 'image'" class="text-xs text-blue-600 dark:text-blue-400">
                      📷
                    </span>
                    <span v-else class="text-xs text-green-600 dark:text-green-400">
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
        </div>

    <!-- Evidence Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
            Evidence for {{ selectedResult?.acceptance_criteria.name }}
          </h3>
          
          <!-- Evidence List -->
          <div class="space-y-2 mb-4">
            <div
              v-for="evidence in selectedResult?.evidence"
              :key="evidence.id"
              class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded"
            >
              <div class="flex items-center space-x-2">
                <span v-if="evidence.type === 'image'" class="text-blue-600 dark:text-blue-400">📷</span>
                <span v-else class="text-green-600 dark:text-green-400">📝</span>
                <span class="text-sm text-gray-900 dark:text-white">
                  {{ evidence.type === 'image' ? 'Image' : 'Text Note' }}
                </span>
              </div>
              <button
                @click="removeEvidence(evidence)"
                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm"
              >
                Remove
              </button>
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
    </layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { reactive, ref } from 'vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'

const props = defineProps({
  testRun: Object,
  summary: Object,
})

const showModal = ref(false)
const selectedResult = ref(null)
const fileInput = ref(null)

const evidenceForm = reactive({
  type: 'image',
  content: '',
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

const updateStatus = (result, status) => {
  router.patch(route('test-result.update', result.id), { status }, {
    preserveState: true,
    preserveScroll: true,
  })
}

const updateNotes = (result, notes) => {
  router.patch(route('test-result.update', result.id), { notes }, {
    preserveState: true,
    preserveScroll: true,
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
</script>
