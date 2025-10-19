<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Acceptance Criteria', url: route('acceptance-criteria.index')},
                    {title: 'Create Acceptance Criteria'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Create Acceptance Criteria</template>

        <form-layout
            :cancel-url="route('acceptance-criteria.index')"
            :submit-text="'Create Acceptance Criteria'"
            :submit-loading-text="'Creating...'"
            :processing="form.processing"
            @submit="submitForm"
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
import { Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'
import formLayout from '@/Shared/FormLayout.vue'
import formField from '@/Shared/FormField.vue'

export default {
    props: [
        'projects',
        'features',
        'errors',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        layout: layout,
        formLayout: formLayout,
        formField: formField,
    },
    setup(props) {
        const form = useForm({
            project_id: '',
            feature_id: '',
            code: '',
            name: '',
            description: '',
        })

        const submitForm = () => {
            form.post(route('acceptance-criteria.store'), {
                onSuccess: () => {
                    // Success handled by controller redirect
                },
            })
        }

        const onProjectChange = () => {
            // Clear feature selection when project changes
            form.feature_id = '';
        }

        const availableFeatures = computed(() => {
            if (!form.project_id) {
                return [];
            }
            return props.features.filter(feature => feature.project_id == form.project_id);
        })

        return {
            form,
            submitForm,
            onProjectChange,
            availableFeatures,
        }
    }
}
</script>
