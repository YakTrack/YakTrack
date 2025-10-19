<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Test Runs', url: route('test-run.index')},
                    {title: 'Create Test Run'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Create Test Run</template>

        <form @submit.prevent="submitForm" class="max-w-2xl">
            <div class="card">
                <div class="card-body">
                    <!-- Project Selection -->
                    <div class="mb-4">
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Project <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="project_id"
                            v-model="form.project_id"
                            @change="loadCriteria"
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

                    <!-- Test Run Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Test Run Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="Enter test run name"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.name }"
                        />
                        <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                            {{ errors.name }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="3"
                            placeholder="Enter test run description"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.description }"
                        ></textarea>
                        <div v-if="errors.description" class="mt-1 text-sm text-red-600">
                            {{ errors.description }}
                        </div>
                    </div>

                    <!-- Execution Date -->
                    <div class="mb-4">
                        <label for="executed_at" class="block text-sm font-medium text-gray-700 mb-2">
                            Execution Date <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="executed_at"
                            v-model="form.executed_at"
                            type="datetime-local"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.executed_at }"
                        />
                        <div v-if="errors.executed_at" class="mt-1 text-sm text-red-600">
                            {{ errors.executed_at }}
                        </div>
                    </div>

                    <!-- Acceptance Criteria Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Acceptance Criteria <span class="text-red-500">*</span>
                        </label>
                        <div v-if="criteria.length === 0" class="text-gray-500 italic">
                            Select a project to see available criteria
                        </div>
                        <div v-else class="space-y-2 max-h-64 overflow-y-auto border border-gray-300 rounded-md p-4">
                            <label
                                v-for="criterion in criteria"
                                :key="criterion.id"
                                class="flex items-start space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded"
                            >
                                <input
                                    :id="`criteria_${criterion.id}`"
                                    v-model="form.acceptance_criteria_ids"
                                    :value="criterion.id"
                                    type="checkbox"
                                    class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                />
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ criterion.name }}
                                    </div>
                                    <div v-if="criterion.code" class="text-xs text-gray-500 font-mono">
                                        {{ criterion.code }}
                                    </div>
                                    <div v-if="criterion.description" class="text-xs text-gray-600 mt-1">
                                        {{ criterion.description.substring(0, 100) }}{{ criterion.description.length > 100 ? '...' : '' }}
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div v-if="errors.acceptance_criteria_ids" class="mt-1 text-sm text-red-600">
                            {{ errors.acceptance_criteria_ids }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3">
                        <Link
                            :href="route('test-run.index')"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Creating...</span>
                            <span v-else>Create Test Run</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </layout>
</template>

<script setup>
import { Link, useForm, router } from '@inertiajs/vue3'
import { reactive, onMounted } from 'vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'

const props = defineProps({
    projects: Array,
    criteria: Array,
    selectedProjectId: [String, Number],
    errors: Object,
})

const form = useForm({
    project_id: props.selectedProjectId || '',
    name: '',
    description: '',
    executed_at: new Date().toISOString().slice(0, 16),
    acceptance_criteria_ids: [],
})

const criteria = reactive(props.criteria || [])

const loadCriteria = () => {
    if (form.project_id) {
        router.get(route('test-run.create'), { project_id: form.project_id }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                criteria.splice(0, criteria.length, ...page.props.criteria)
            },
        })
    } else {
        criteria.splice(0, criteria.length)
    }
}

const submitForm = () => {
    form.post(route('test-run.store'), {
        onSuccess: () => {
            // Success handled by controller redirect
        },
    })
}

onMounted(() => {
    if (form.project_id) {
        loadCriteria()
    }
})
</script>


