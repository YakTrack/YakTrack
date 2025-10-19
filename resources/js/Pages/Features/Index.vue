<template>
    <layout>
        <breadcrumbs :items="breadcrumbs" />

        <!-- Header -->
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Features</h1>
                <Link
                    :href="route('features.create')"
                    class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Create Feature
                </Link>
            </div>
        </div>

        <!-- Filters -->
        <div class="card-body">
            <form @submit.prevent="filterFeatures" class="flex gap-4 mb-6">
                <div class="flex-1">
                    <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Project
                    </label>
                    <select
                        id="project_id"
                        v-model="filters.project_id"
                        @change="onProjectChange"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option value="">All Projects</option>
                        <option v-for="project in projects" :key="project.id" :value="project.id">
                            {{ project.name }}
                        </option>
                    </select>
                </div>
                <button
                    type="submit"
                    class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Filter
                </button>
            </form>
        </div>

        <!-- Features List -->
        <div class="card" v-if="features.data.length">
            <table class="table card-body">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Project</th>
                        <th>Description</th>
                        <th>Acceptance Criteria</th>
                        <th><span class="float-right">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="feature in features.data" :key="feature.id" class="item-container">
                        <td>
                            <Link :href="route('features.show', feature.id)">
                                {{ feature.name }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('project.show', feature.project)" v-if="feature.project">
                                {{ feature.project.name }}
                            </Link>
                        </td>
                        <td>
                            <span v-if="feature.description" class="text-gray-600">
                                {{ feature.description }}
                            </span>
                            <span v-else class="text-gray-400 italic">No description</span>
                        </td>
                        <td>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ feature.acceptance_criteria_count }}
                            </span>
                        </td>
                        <td>
                            <div class="flex justify-end gap-2">
                                <button-link
                                    :href="route('features.edit', feature.id)"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    <i class="fa fa-edit text-gray-600 text-xs"></i>
                                </button-link>
                                <button-link
                                    :href="route('features.show', feature.id)"
                                    class="text-gray-600 hover:text-gray-900"
                                >
                                    <i class="fa fa-eye text-gray-600 text-xs"></i>
                                </button-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any features yet.
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
        'features',
        'projects',
        'filters',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        layout: layout,
    },
    setup(props) {
        const filters = reactive({
            project_id: props.filters.project_id || '',
        })

        const filterFeatures = () => {
            router.get(route('features.index'), filters, {
                preserveState: true,
                preserveScroll: true,
            })
        }

        const onProjectChange = () => {
            filterFeatures()
        }

        return {
            filters,
            filterFeatures,
            onProjectChange,
        }
    },
    computed: {
        breadcrumbs() {
            return [
                { label: 'Features', href: route('features.index') },
            ]
        },
    },
}
</script>
