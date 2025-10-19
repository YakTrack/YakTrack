<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Acceptance Criteria', url: route('acceptance-criteria.index')},
                    {title: criteria ? 'Edit Acceptance Criteria' : 'Create Acceptance Criteria'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ criteria ? 'Edit Acceptance Criteria' : 'Create Acceptance Criteria' }}</template>

        <form @submit.prevent="submit" class="max-w-2xl">
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
                            @change="onProjectChange"
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

                    <!-- Feature Selection -->
                    <div class="mb-4">
                        <label for="feature_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Feature
                        </label>
                        <select
                            id="feature_id"
                            v-model="form.feature_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.feature_id }"
                            :disabled="!form.project_id"
                        >
                            <option value="">No feature (optional)</option>
                            <option v-for="feature in availableFeatures" :key="feature.id" :value="feature.id">
                                {{ feature.name }}
                            </option>
                        </select>
                        <div v-if="errors.feature_id" class="mt-1 text-sm text-red-600">
                            {{ errors.feature_id }}
                        </div>
                        <p v-if="!form.project_id" class="mt-1 text-sm text-gray-500">
                            Please select a project first to see available features.
                        </p>
                    </div>

                    <!-- Code -->
                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                            Code
                        </label>
                        <input
                            id="code"
                            v-model="form.code"
                            type="text"
                            placeholder="e.g., AC-001"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.code }"
                        />
                        <div v-if="errors.code" class="mt-1 text-sm text-red-600">
                            {{ errors.code }}
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            Optional unique identifier for this criteria within the project
                        </p>
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            placeholder="Enter acceptance criteria name"
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
                            rows="4"
                            placeholder="Enter detailed description of the acceptance criteria"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.description }"
                        ></textarea>
                        <div v-if="errors.description" class="mt-1 text-sm text-red-600">
                            {{ errors.description }}
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end space-x-3">
                        <button-link
                            :href="criteria && criteria.id ? route('acceptance-criteria.show', criteria.id) : route('acceptance-criteria.index')"
                            color="gray"
                        >
                            Cancel
                        </button-link>
                        <button
                            type="submit"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            {{ isCreateForm ? 'Create' : 'Update' }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'

export default {
    props: [
        'criteria',
        'projects',
        'features',
        'errors',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        layout: layout,
    },
    data() {
        return {
            form: this.criteria ? {
                project_id: this.criteria.project_id || '',
                feature_id: this.criteria.feature_id || '',
                code: this.criteria.code || '',
                name: this.criteria.name || '',
                description: this.criteria.description || '',
            } : {
                project_id: '',
                feature_id: '',
                code: '',
                name: '',
                description: '',
            }
        }
    },
    computed: {
        isCreateForm() {
            return this.criteria == null;
        },
        availableFeatures() {
            if (!this.form.project_id) {
                return [];
            }
            return this.features.filter(feature => feature.project_id == this.form.project_id);
        },
    },
    methods: {
        submit() {
            const verb = this.isCreateForm ? 'post' : 'patch';
            const url = this.isCreateForm 
                ? route('acceptance-criteria.store') 
                : route('acceptance-criteria.update', this.criteria.id);

            this.$inertia[verb](url, this.form);
        },
        onProjectChange() {
            // Clear feature selection when project changes
            this.form.feature_id = '';
        }
    }
}
</script>


