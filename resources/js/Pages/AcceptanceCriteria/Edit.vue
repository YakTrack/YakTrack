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

        <form-layout
            :cancel-url="criteria && criteria.id ? route('acceptance-criteria.show', criteria.id) : route('acceptance-criteria.index')"
            :submit-text="isCreateForm ? 'Create' : 'Update'"
            :submit-loading-text="'Saving...'"
            :processing="processing"
            @submit="submit"
        >
            <!-- Project Selection -->
            <form-field
                v-model="form.project_id"
                type="select"
                label="Project"
                placeholder="Select a project"
                :options="projects"
                :error="errors.project_id"
                :required="true"
                @update:model-value="onProjectChange"
            />

            <!-- Feature Selection -->
            <form-field
                v-model="form.feature_id"
                type="select"
                label="Feature"
                placeholder="No feature (optional)"
                :options="availableFeatures"
                :error="errors.feature_id"
                :disabled="!form.project_id"
            >
                <template #help>
                    <p v-if="!form.project_id" class="mt-1 text-sm text-gray-500">
                        Please select a project first to see available features.
                    </p>
                </template>
            </form-field>

            <!-- Code -->
            <form-field
                v-model="form.code"
                type="text"
                label="Code"
                placeholder="e.g., AC-001"
                :error="errors.code"
                help="Optional unique identifier for this criteria within the project"
            />

            <!-- Name -->
            <form-field
                v-model="form.name"
                type="text"
                label="Name"
                placeholder="Enter acceptance criteria name"
                :error="errors.name"
                :required="true"
            />

            <!-- Description -->
            <form-field
                v-model="form.description"
                type="textarea"
                label="Description"
                placeholder="Enter detailed description of the acceptance criteria"
                :error="errors.description"
                :rows="4"
            />
        </form-layout>
    </layout>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'
import formLayout from '@/Shared/FormLayout.vue'
import formField from '@/Shared/FormField.vue'

export default {
    props: [
        'criteria',
        'projects',
        'features',
        'errors',
        'prefill',
        'focusedClientId',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        layout: layout,
        formLayout: formLayout,
        formField: formField,
    },
    data() {
        const focusedProjectId = this.focusedClientId
            ? (this.projects.find(project => project.client_id == this.focusedClientId)?.id ?? '')
            : '';

        return {
            form: this.criteria ? {
                project_id: this.criteria.project_id || '',
                feature_id: this.criteria.feature_id || '',
                code: this.criteria.code || '',
                name: this.criteria.name || '',
                description: this.criteria.description || '',
            } : {
                project_id: this.prefill?.project_id || focusedProjectId,
                feature_id: this.prefill?.feature_id || '',
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
        processing() {
            return this.$inertia.processing;
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


