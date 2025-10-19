<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Acceptance Criteria', url: route('acceptance-criteria.index')},
                    {title: 'Import from Gherkin'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Import Acceptance Criteria from Gherkin</template>

        <div class="max-w-4xl">
            <!-- Instructions -->
            <div class="card mb-6">
                <div class="card-body">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">How to Import</h3>
                    <div class="prose prose-sm max-w-none">
                        <p class="text-gray-600 mb-4">
                            Upload a Gherkin feature file (.feature or .txt) to automatically create acceptance criteria. 
                            Each scenario in your feature file will become a separate acceptance criterion.
                        </p>
                        
                        <h4 class="text-md font-medium text-gray-900 mb-2">Supported Gherkin Format:</h4>
                        <pre class="bg-gray-100 p-4 rounded text-sm overflow-x-auto"><code>Feature: User Authentication
  As a user
  I want to be able to log in
  So that I can access my account

  Scenario: Successful login with valid credentials
    Given I am on the login page
    When I enter valid username and password
    Then I should be redirected to the dashboard

  Scenario: Failed login with invalid credentials
    Given I am on the login page
    When I enter invalid username and password
    Then I should see an error message</code></pre>
                    </div>
                </div>
            </div>

            <!-- Import Form -->
            <form @submit.prevent="submit" class="card">
                <div class="card-body">
                    <!-- Project Selection -->
                    <div class="mb-6">
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="project_id"
                            v-model="form.project_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.project_id }"
                        >
                            <option value="">Select a project</option>
                            <option v-for="project in projects" :key="project.id" :value="project.id">
                                {{ project.name }}
                            </option>
                        </select>
                        <div v-if="errors.project_id" class="mt-1 text-sm text-red-600">
                            {{ errors.project_id }}
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-6">
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            Gherkin File <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors"
                             :class="{ 'border-red-500': errors.file }">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span>Upload a file</span>
                                        <input 
                                            id="file" 
                                            ref="fileInput"
                                            type="file" 
                                            accept=".feature,.txt"
                                            @change="handleFileChange"
                                            class="sr-only"
                                        />
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    .feature or .txt files up to 10MB
                                </p>
                            </div>
                        </div>
                        <div v-if="selectedFile" class="mt-2 text-sm text-gray-600">
                            Selected: {{ selectedFile.name }}
                        </div>
                        <div v-if="errors.file" class="mt-1 text-sm text-red-600">
                            {{ errors.file }}
                        </div>
                    </div>

                    <!-- Overwrite Option -->
                    <div class="mb-6">
                        <div class="flex items-center">
                            <input
                                id="overwrite_existing"
                                v-model="form.overwrite_existing"
                                type="checkbox"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label for="overwrite_existing" class="ml-2 block text-sm text-gray-900">
                                Overwrite existing acceptance criteria with the same code
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            If unchecked, existing criteria will be skipped during import.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-3">
                        <button-link
                            :href="route('acceptance-criteria.index')"
                            color="gray"
                        >
                            Cancel
                        </button-link>
                        <button
                            type="submit"
                            :disabled="!form.project_id || !selectedFile || processing"
                            class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <span v-if="processing">
                                <i class="fa fa-spinner fa-spin mr-2"></i>
                                Importing...
                            </span>
                            <span v-else>
                                <i class="fa fa-upload mr-2"></i>
                                Import Criteria
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </layout>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import ButtonLink from '@/Shared/ButtonLink.vue'

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
})

const form = reactive({
    project_id: '',
    file: null,
    overwrite_existing: false,
})

const errors = ref({})
const selectedFile = ref(null)
const processing = ref(false)

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.file = file
        selectedFile.value = file
    }
}

const submit = () => {
    processing.value = true
    errors.value = {}

    const formData = new FormData()
    formData.append('project_id', form.project_id)
    formData.append('file', form.file)
    formData.append('overwrite_existing', form.overwrite_existing ? '1' : '0')

    router.post(route('acceptance-criteria.import.process'), formData, {
        onError: (err) => {
            errors.value = err
            processing.value = false
        },
        onFinish: () => {
            processing.value = false
        },
    })
}
</script>

