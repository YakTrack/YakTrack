<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Features'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Features </template>
        <template #top-right-toolbar>
            <button-link :href="route('features.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Feature
            </button-link>
        </template>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form @submit.prevent="filterFeatures" class="flex gap-4 items-end">
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
                    <button-link
                        :href="route('features.index')"
                        color="gray"
                    >
                        Clear
                    </button-link>
                </form>
            </div>
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
                            <div class="float-right">
                                <actions-dropdown :options="getFeatureActions(feature)" direction="left"></actions-dropdown>
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
import actionsDropdown from '@/Shared/ActionsDropdown.vue'

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
        actionsDropdown: actionsDropdown,
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

        const getFeatureActions = (feature) => {
            return [
                {
                    name: 'Edit Feature',
                    callback: () => {
                        router.visit(route('features.edit', feature.id));
                    }
                },
                {
                    name: 'View Feature',
                    callback: () => {
                        router.visit(route('features.show', feature.id));
                    }
                }
            ];
        }

        return {
            filters,
            filterFeatures,
            onProjectChange,
            getFeatureActions,
        }
    }
}
</script>
