<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    { title: 'Home', url: route('home') },
                    { title: 'Projects' },
                ]"
            />
        </template>
        <template #title>Projects</template>
        <template #top-right-toolbar>
            <div class="flex flex-wrap gap-2">
                <button-link :href="route('project.archived')" color="gray">
                    <i class="fa fa-archive mr-2"></i>
                    View Archived
                </button-link>
                <button-link :href="route('project.create')" color="blue">
                    <i class="fa fa-plus mr-2 text-blue-100"></i>
                    Create Project
                </button-link>
            </div>
        </template>

        <div
            class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
        >
            <data-table-toolbar
                v-model="filters.q"
                search-placeholder="Search projects or clients…"
                :show-clear="hasActiveFilters"
                @search="onSearchInput"
                @clear="clearFilters"
            >
                <template #filters>
                    <div class="min-w-[200px]">
                        <label for="project-client-filter" class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400">
                            Client
                        </label>
                        <select
                            id="project-client-filter"
                            v-model="filters.client_id"
                            class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            @change="applyFilters"
                        >
                            <option value="">All clients</option>
                            <option v-for="c in clients" :key="c.id" :value="String(c.id)">
                                {{ c.name }}
                            </option>
                        </select>
                    </div>
                </template>
            </data-table-toolbar>

            <div v-if="projects.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50/90 dark:bg-gray-800/60">
                        <tr>
                            <sortable-column-header
                                label="Name"
                                column="name"
                                :sort="sort"
                                :direction="direction"
                                @sort="toggleSort($event)"
                            />
                            <sortable-column-header
                                label="Client"
                                column="client"
                                :sort="sort"
                                :direction="direction"
                                @sort="toggleSort($event)"
                            />
                            <th
                                class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                                scope="col"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-800 dark:bg-gray-900">
                        <tr
                            v-for="project in projects.data"
                            :key="project.id"
                            class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                        >
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <Link
                                    :href="route('project.show', { project: project.id })"
                                    class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    {{ project.name }}
                                </Link>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                <Link
                                    v-if="project.client"
                                    :href="route('client.show', { client: project.client.id })"
                                    class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    {{ project.client.name }}
                                </Link>
                                <span v-else class="text-gray-400 dark:text-gray-500">—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                <actions-dropdown :options="getProjectActions(project)" direction="left" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="px-6 py-16 text-center">
                <div
                    class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800"
                >
                    <i class="fa fa-folder-open text-2xl text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ hasActiveFilters ? 'No matching projects' : 'No projects yet' }}
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{
                        hasActiveFilters
                            ? 'Try adjusting your search or client filter.'
                            : 'Create a project to start tracking work for a client.'
                    }}
                </p>
                <div v-if="!hasActiveFilters" class="mt-6">
                    <button-link :href="route('project.create')" color="blue">
                        <i class="fa fa-plus mr-2 text-blue-100"></i>
                        Create Project
                    </button-link>
                </div>
                <button
                    v-else
                    type="button"
                    class="mt-6 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                    @click="clearFilters"
                >
                    Clear filters
                </button>
            </div>

            <inertia-table-pagination
                v-if="projects.data.length"
                :paginator="projects"
                :per-page="perPage"
                @per-page="setPerPage"
            />
        </div>

        <confirm-modal
            :is-open="showConfirmModal"
            :title="confirmModalTitle"
            :description="confirmModalDescription"
            :confirm-text="confirmModalConfirmText"
            @close="closeConfirmModal"
            @confirm="executeConfirmAction"
        />
    </layout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import DataTableToolbar from '@/components/data-table/DataTableToolbar.vue'
import InertiaTablePagination from '@/components/data-table/InertiaTablePagination.vue'
import SortableColumnHeader from '@/components/data-table/SortableColumnHeader.vue'
import { useInertiaTable } from '@/composables/useInertiaTable'
import actionsDropdown from '@/Shared/ActionsDropdown.vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import confirmModal from '@/Shared/ConfirmModal.vue'
import layout from '@/Shared/Layout.vue'

defineProps({
    projects: {
        type: Object,
        required: true,
    },
    clients: {
        type: Array,
        default: () => [],
    },
})

const {
    filters,
    sort,
    direction,
    perPage,
    hasActiveFilters,
    onSearchInput,
    applyFilters,
    clearFilters,
    toggleSort,
    setPerPage,
} = useInertiaTable({
    routeName: 'project.index',
    filterDefaults: { q: '', client_id: '' },
})

const showConfirmModal = ref(false)
const confirmModalTitle = ref('')
const confirmModalDescription = ref('')
const confirmModalConfirmText = ref('Confirm')
const pendingAction = ref(null)

const openConfirmModal = (title, description, confirmText, action) => {
    confirmModalTitle.value = title
    confirmModalDescription.value = description
    confirmModalConfirmText.value = confirmText
    pendingAction.value = action
    showConfirmModal.value = true
}

const closeConfirmModal = () => {
    showConfirmModal.value = false
    pendingAction.value = null
}

const executeConfirmAction = () => {
    if (pendingAction.value) {
        pendingAction.value()
    }
    closeConfirmModal()
}

const getProjectActions = (project) => {
    const actions = [
        {
            name: 'Edit Project',
            callback: () => {
                router.visit(route('project.edit', { project }))
            },
        },
    ]

    if (project.isDeletable) {
        actions.push({
            name: 'Delete Project',
            callback: () => {
                openConfirmModal(
                    'Delete Project',
                    'Are you sure you want to delete this project?',
                    'Delete',
                    () => {
                        router.delete(route('project.destroy', project.id))
                    },
                )
            },
        })
    }

    if (project.isArchived) {
        actions.push({
            name: 'Unarchive Project',
            callback: () => {
                openConfirmModal(
                    'Unarchive Project',
                    'Are you sure you want to unarchive this project?',
                    'Unarchive',
                    () => {
                        router.patch(route('project.unarchive', project.id))
                    },
                )
            },
        })
    } else {
        actions.push({
            name: 'Archive Project',
            callback: () => {
                openConfirmModal(
                    'Archive Project',
                    'Are you sure you want to archive this project?',
                    'Archive',
                    () => {
                        router.patch(route('project.archive', project.id))
                    },
                )
            },
        })
    }

    return actions
}
</script>
