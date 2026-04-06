<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    { title: 'Home', url: route('home') },
                    { title: 'Sprints' },
                ]"
            />
        </template>
        <template #title>Sprints</template>
        <template #top-right-toolbar>
            <button-link :href="route('sprint.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Sprint
            </button-link>
        </template>

        <div
            class="item-type-container overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
            data-item-type="sprint"
        >
            <data-table-toolbar
                v-model="filters.q"
                search-placeholder="Search sprints or projects…"
                :show-clear="hasActiveFilters"
                @search="onSearchInput"
                @clear="clearFilters"
            >
                <template #filters>
                    <div class="min-w-[200px]">
                        <label
                            for="sprint-project-filter"
                            class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400"
                        >
                            Project
                        </label>
                        <select
                            id="sprint-project-filter"
                            v-model="filters.project_id"
                            class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            @change="applyFilters"
                        >
                            <option value="">All projects</option>
                            <option v-for="p in projects" :key="p.id" :value="String(p.id)">
                                {{ p.name }}
                            </option>
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <label
                            for="sprint-lifecycle-filter"
                            class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400"
                        >
                            State
                        </label>
                        <select
                            id="sprint-lifecycle-filter"
                            v-model="filters.lifecycle"
                            class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            @change="applyFilters"
                        >
                            <option value="">All</option>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </template>
            </data-table-toolbar>

            <div v-if="sprints.data.length" class="overflow-x-auto">
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
                                label="Project"
                                column="project"
                                :sort="sort"
                                :direction="direction"
                                @sort="toggleSort($event)"
                            />
                            <sortable-column-header
                                label="Status"
                                column="status"
                                :sort="sort"
                                :direction="direction"
                                @sort="toggleSort($event)"
                            />
                            <sortable-column-header
                                label="Total hours"
                                column="duration"
                                align="right"
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
                            v-for="sprint in sprints.data"
                            :key="sprint.id"
                            class="item-container transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                            :data-item-name="sprint.name"
                            :data-item-destroy-route="route('sprint.destroy', sprint.id)"
                        >
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <Link
                                    :href="route('sprint.show', sprint.id)"
                                    class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    {{ sprint.name }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                <template v-if="sprint.projects && sprint.projects.length">
                                    <template v-for="(project, index) in sprint.projects" :key="project.id">
                                        <Link
                                            :href="route('project.show', project.id)"
                                            class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            {{ project.name }}
                                        </Link>
                                        <span v-if="index < sprint.projects.length - 1">, </span>
                                    </template>
                                </template>
                                <span v-else class="text-gray-400 dark:text-gray-500">—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                <span
                                    v-if="sprint.is_open"
                                    class="font-medium text-green-600 dark:text-green-400"
                                >
                                    Open
                                </span>
                                <span v-else class="font-medium text-gray-500 dark:text-gray-400"> Closed </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right font-mono text-sm text-gray-800 dark:text-gray-200">
                                {{ sprint.totalDurationForHumans }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                <actions-dropdown :options="getSprintActions(sprint)" direction="left" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="px-6 py-16 text-center">
                <div
                    class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800"
                >
                    <i class="fa fa-calendar text-2xl text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ hasActiveFilters ? 'No matching sprints' : 'No sprints yet' }}
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{
                        hasActiveFilters
                            ? 'Try adjusting your search or filters.'
                            : 'Create a sprint to group work across one or more projects.'
                    }}
                </p>
                <div v-if="!hasActiveFilters" class="mt-6">
                    <button-link :href="route('sprint.create')" color="blue">
                        <i class="fa fa-plus mr-2 text-blue-100"></i>
                        Create Sprint
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
                v-if="sprints.data.length"
                :paginator="sprints"
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
    sprints: {
        type: Object,
        required: true,
    },
    projects: {
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
    routeName: 'sprint.index',
    filterDefaults: { q: '', project_id: '', lifecycle: '' },
    sortDefaults: { sort: 'id', direction: 'desc' },
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

const getSprintActions = (sprint) => [
    {
        name: 'View Sprint',
        callback: () => {
            router.visit(route('sprint.show', sprint.id))
        },
    },
    {
        name: 'Edit Sprint',
        callback: () => {
            router.visit(route('sprint.edit', sprint.id))
        },
    },
    {
        name: 'Delete Sprint',
        callback: () => {
            openConfirmModal(
                'Delete Sprint',
                'Are you sure you want to delete this sprint?',
                'Delete',
                () => {
                    router.delete(route('sprint.destroy', sprint.id))
                },
            )
        },
    },
]
</script>
