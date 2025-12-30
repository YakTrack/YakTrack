<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="breadcrumbItems"
            ></breadcrumbs>
        </template>
        <template #title>
            {{ feature ? 'Edit Feature' : 'Create Feature' }}
        </template>

        <form-layout
            :cancel-url="feature ? route('features.show', feature.id) : route('features.index')"
            :submit-text="feature ? 'Update Feature' : 'Create Feature'"
            :submit-loading-text="'Saving...'"
            :processing="processing"
            @submit="submit"
        >
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Project Selection -->
                <div class="md:col-span-2">
                    <form-field
                        v-model="form.project_id"
                        type="select"
                        label="Project"
                        placeholder="Select a project"
                        :options="projects"
                        :error="errors.project_id"
                        :required="true"
                    />
                </div>

                <!-- Code -->
                <div>
                    <form-field
                        v-model="form.code"
                        type="text"
                        label="Code"
                        placeholder="Enter feature code (optional)"
                        :error="errors.code"
                    />
                </div>

                <!-- Feature Name -->
                <div>
                    <form-field
                        v-model="form.name"
                        type="text"
                        label="Feature Name"
                        placeholder="Enter feature name"
                        :error="errors.name"
                        :required="true"
                    />
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <form-field
                        v-model="form.description"
                        type="textarea"
                        label="Description"
                        placeholder="Enter feature description (optional)"
                        :error="errors.description"
                        :rows="4"
                    />
                </div>
            </div>
        </form-layout>
    </layout>
</template>

<script>
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'
import formLayout from '@/Shared/FormLayout.vue'
import formField from '@/Shared/FormField.vue'

export default {
    props: [
        'feature',
        'projects',
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
        const form = reactive({
            project_id: props.feature?.project_id || '',
            code: props.feature?.code || '',
            name: props.feature?.name || '',
            description: props.feature?.description || '',
        })

        const submit = () => {
            if (props.feature) {
                router.put(route('features.update', props.feature.id), form)
            } else {
                router.post(route('features.store'), form)
            }
        }

        return {
            form,
            submit,
        }
    },
    computed: {
        breadcrumbItems() {
            if (this.feature) {
                return [
                    { title: 'Home', url: route('home') },
                    { title: 'Features', url: route('features.index') },
                    { title: this.feature.name, url: route('features.show', this.feature.id) },
                    { title: 'Edit' },
                ]
            } else {
                return [
                    { title: 'Home', url: route('home') },
                    { title: 'Features', url: route('features.index') },
                    { title: 'Create Feature' },
                ]
            }
        },
        processing() {
            return this.$inertia.processing
        },
    },
}
</script>
