<template>
    <layout>
        <breadcrumbs :items="breadcrumbs" />

        <!-- Header -->
        <div class="card-header">
            <h1 class="text-2xl font-bold text-gray-900">
                {{ feature ? 'Edit Feature' : 'Create Feature' }}
            </h1>
        </div>

        <!-- Form -->
        <div class="card-body">
            <form @submit.prevent="submit">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Project -->
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">
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

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Feature Name <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.name }"
                            placeholder="Enter feature name"
                        />
                        <div v-if="errors.name" class="mt-1 text-sm text-red-600">
                            {{ errors.name }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea
                            id="description"
                            v-model="form.description"
                            rows="4"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            :class="{ 'border-red-500': errors.description }"
                            placeholder="Enter feature description (optional)"
                        ></textarea>
                        <div v-if="errors.description" class="mt-1 text-sm text-red-600">
                            {{ errors.description }}
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-4 mt-6">
                    <Link
                        :href="feature ? route('features.show', feature.id) : route('features.index')"
                        class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        :disabled="processing"
                    >
                        {{ processing ? 'Saving...' : (feature ? 'Update Feature' : 'Create Feature') }}
                    </button>
                </div>
            </form>
        </div>
    </layout>
</template>

<script>
import { Link, router } from '@inertiajs/vue3'
import { reactive } from 'vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'

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
    },
    setup(props) {
        const form = reactive({
            project_id: props.feature?.project_id || '',
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
        breadcrumbs() {
            if (this.feature) {
                return [
                    { label: 'Features', href: route('features.index') },
                    { label: this.feature.name, href: route('features.show', this.feature.id) },
                    { label: 'Edit', href: route('features.edit', this.feature.id) },
                ]
            } else {
                return [
                    { label: 'Features', href: route('features.index') },
                    { label: 'Create', href: route('features.create') },
                ]
            }
        },
        processing() {
            return this.$inertia.processing
        },
    },
}
</script>
