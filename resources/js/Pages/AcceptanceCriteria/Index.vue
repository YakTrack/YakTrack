<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Acceptance Criteria'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Acceptance Criteria</template>
        <template #top-right-toolbar>
            <button-link :href="route('acceptance-criteria.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Criteria
            </button-link>
        </template>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <form @submit.prevent="filterCriteria" class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Project
                        </label>
                        <select
                            id="project_id"
                            v-model="filters.project_id"
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
                        :href="route('acceptance-criteria.index')"
                        color="gray"
                    >
                        Clear
                    </button-link>
                </form>
            </div>
        </div>

        <!-- Criteria List -->
        <div class="card" v-if="criteria.data.length">
            <table class="table card-body">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Project</th>
                        <th>Versions</th>
                        <th>Linked Tasks</th>
                        <th><span class="float-right">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="criterion in criteria.data" :key="criterion.id" class="item-container">
                        <td>
                            <span v-if="criterion.code" class="font-mono bg-gray-100 px-2 py-1 rounded text-xs">
                                {{ criterion.code }}
                            </span>
                            <span v-else class="text-gray-400 italic">No code</span>
                        </td>
                        <td>
                            <Link :href="route('acceptance-criteria.show', criterion.id)">
                                {{ criterion.name }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('project.show', criterion.project)" v-if="criterion.project">
                                {{ criterion.project.name }}
                            </Link>
                        </td>
                        <td>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ criterion.version_count }}
                            </span>
                        </td>
                        <td>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                {{ criterion.linked_tasks_count }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group float-right">
                                <button-link
                                    :href="route('acceptance-criteria.edit', criterion.id)"
                                >
                                    <i class="fa fa-edit text-gray-600 text-xs"></i>
                                </button-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any acceptance criteria yet.
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
        'criteria',
        'projects',
        'filters',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        layout: layout,
    },
    setup() {
        const filters = reactive({
            project_id: '',
        })

        const filterCriteria = () => {
            router.get(route('acceptance-criteria.index'), filters, {
                preserveState: true,
                preserveScroll: true,
            })
        }

        return {
            filters,
            filterCriteria,
        }
    }
}
</script>
