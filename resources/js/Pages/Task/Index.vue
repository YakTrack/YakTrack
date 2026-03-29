<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    { title: 'Home', url: route('home') },
                    { title: 'Tasks' },
                ]"
            />
        </template>
        <template #title>Tasks</template>
        <template #top-right-toolbar>
            <div class="flex flex-wrap items-center gap-3">
                <span
                    v-if="selectedTaskIds.length"
                    class="hidden text-sm text-gray-600 dark:text-gray-400 sm:inline"
                >
                    {{ selectedTaskIds.length }} selected
                </span>
                <span
                    v-if="tasks.data.length && selectedTaskIds.length && projects.length"
                    class="inline-flex"
                    title="Bulk actions"
                >
                    <actions-dropdown :options="bulkActionsOptions" direction="left" />
                </span>
                <button-link :href="route('task.create')" color="blue">
                    <i class="fa fa-plus text-blue-100 mr-2"></i>
                    Create Task
                </button-link>
            </div>
        </template>

        <div
            class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
        >
            <data-table-toolbar
                v-model="filters.q"
                search-placeholder="Search tasks, projects, or clients…"
                :show-clear="hasActiveFilters"
                @search="onSearchInput"
                @clear="clearFilters"
            >
                <template #filters>
                    <div class="min-w-[200px]">
                        <label
                            for="task-project-filter"
                            class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400"
                        >
                            Project
                        </label>
                        <select
                            id="task-project-filter"
                            v-model="filters.project_id"
                            class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            @change="onProjectFilterChange"
                        >
                            <option value="">All projects</option>
                            <option v-for="p in projects" :key="p.id" :value="String(p.id)">
                                {{ p.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="filters.project_id" class="min-w-[200px]">
                        <label
                            for="task-status-filter"
                            class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400"
                        >
                            Status
                        </label>
                        <select
                            id="task-status-filter"
                            v-model="filters.status_id"
                            class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            @change="applyFilters"
                        >
                            <option value="">All statuses</option>
                            <option v-for="s in statuses" :key="s.id" :value="String(s.id)">
                                {{ s.name }}
                            </option>
                        </select>
                    </div>
                </template>
            </data-table-toolbar>

            <div v-if="tasks.data.length" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                    <thead class="bg-gray-50/90 dark:bg-gray-800/60">
                        <tr>
                            <th class="w-12 px-4 py-3" scope="col">
                                <input
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800"
                                    :checked="allSelected"
                                    @change="toggleSelectAll($event.target.checked)"
                                />
                            </th>
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
                                label="Client"
                                column="client"
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
                            v-for="task in tasks.data"
                            :key="task.id"
                            class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                        >
                            <td class="whitespace-nowrap px-4 py-3">
                                <input
                                    v-model="selectedTaskIds"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-800"
                                    :value="task.id"
                                />
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900 dark:text-gray-100">
                                <Link
                                    :href="route('task.show', task)"
                                    class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    {{ task.shortName }}
                                </Link>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                <Link
                                    v-if="task.project"
                                    :href="route('project.show', task.project)"
                                    class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    {{ task.project.name }}
                                </Link>
                                <span v-else class="text-gray-400 dark:text-gray-500">—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                <Link
                                    v-if="task.project && task.project.client"
                                    :href="route('client.show', task.project.client)"
                                    class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                                >
                                    {{ task.project.client.name }}
                                </Link>
                                <span v-else class="text-gray-400 dark:text-gray-500">—</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-sm">
                                <span
                                    v-if="task.task_status"
                                    class="inline-block rounded-full px-2 py-1 text-xs text-white"
                                    :style="{ backgroundColor: task.task_status.color }"
                                >
                                    {{ task.task_status.name }}
                                </span>
                                <span
                                    v-else
                                    class="inline-block rounded-full bg-gray-300 px-2 py-1 text-xs text-gray-700 dark:bg-gray-600 dark:text-gray-200"
                                >
                                    No status
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                <actions-dropdown :options="getTaskActions(task)" direction="left" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="px-6 py-16 text-center">
                <div
                    class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800"
                >
                    <i class="fa fa-tasks text-2xl text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    {{ hasActiveFilters ? 'No matching tasks' : 'No tasks yet' }}
                </h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{
                        hasActiveFilters
                            ? 'Try adjusting your search or filters.'
                            : 'Create a task and log time against it from a project.'
                    }}
                </p>
                <div v-if="!hasActiveFilters" class="mt-6">
                    <button-link :href="route('task.create')" color="blue">
                        <i class="fa fa-plus mr-2 text-blue-100"></i>
                        Create Task
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
                v-if="tasks.data.length"
                :paginator="tasks"
                :per-page="perPage"
                @per-page="setPerPage"
            />
        </div>

        <modal
            :is-open="showBulkAssignModal"
            title="Assign tasks to project"
            :description="bulkAssignModalDescription"
            :show-default-footer="true"
            cancel-text="Cancel"
            confirm-text="Assign"
            :confirm-loading="bulkAssignSubmitting"
            :close-on-backdrop="true"
            :close-on-escape="true"
            @close="closeBulkAssignModal"
            @confirm="confirmBulkAssign"
        >
            <div class="mt-2">
                <label
                    for="bulk-assign-project"
                    class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
                >
                    Project
                </label>
                <select
                    id="bulk-assign-project"
                    v-model="bulkProjectId"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100"
                >
                    <option value="">Select a project</option>
                    <option v-for="project in projects" :key="project.id" :value="project.id">
                        {{ project.name }}
                    </option>
                </select>
            </div>
        </modal>

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
import { computed, ref } from 'vue'
import DataTableToolbar from '@/components/data-table/DataTableToolbar.vue'
import InertiaTablePagination from '@/components/data-table/InertiaTablePagination.vue'
import SortableColumnHeader from '@/components/data-table/SortableColumnHeader.vue'
import Modal from '@/components/Modal.vue'
import { useInertiaTable } from '@/composables/useInertiaTable'
import actionsDropdown from '@/Shared/ActionsDropdown.vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import confirmModal from '@/Shared/ConfirmModal.vue'
import layout from '@/Shared/Layout.vue'

const props = defineProps({
    tasks: {
        type: Object,
        required: true,
    },
    projects: {
        type: Array,
        default: () => [],
    },
    statuses: {
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
    routeName: 'task.index',
    filterDefaults: { q: '', project_id: '', status_id: '' },
    sortDefaults: { sort: 'id', direction: 'desc' },
})

const selectedTaskIds = ref([])
const bulkProjectId = ref('')
const bulkAssignSubmitting = ref(false)
const showBulkAssignModal = ref(false)
const showConfirmModal = ref(false)
const confirmModalTitle = ref('')
const confirmModalDescription = ref('')
const confirmModalConfirmText = ref('Confirm')
const pendingAction = ref(null)

const allSelected = computed(
    () =>
        props.tasks.data.length > 0 &&
        selectedTaskIds.value.length === props.tasks.data.length &&
        props.tasks.data.every((t) => selectedTaskIds.value.includes(t.id)),
)

const bulkActionsOptions = computed(() => [
    {
        name: 'Assign to project…',
        callback: () => openBulkAssignModal(),
    },
])

const bulkAssignModalDescription = computed(() => {
    const n = selectedTaskIds.value.length
    const label = n === 1 ? 'task' : 'tasks'

    return `Choose which project to assign ${n} selected ${label} to.`
})

const onProjectFilterChange = () => {
    filters.status_id = ''
    applyFilters()
}

const toggleSelectAll = (checked) => {
    selectedTaskIds.value = checked ? props.tasks.data.map((t) => t.id) : []
}

const openBulkAssignModal = () => {
    bulkProjectId.value = ''
    showBulkAssignModal.value = true
}

const closeBulkAssignModal = () => {
    showBulkAssignModal.value = false
    bulkProjectId.value = ''
    bulkAssignSubmitting.value = false
}

const confirmBulkAssign = () => {
    if (bulkProjectId.value === '' || bulkProjectId.value === null) {
        return
    }
    if (bulkAssignSubmitting.value) {
        return
    }
    bulkAssignSubmitting.value = true
    router.patch(
        route('task.bulk-assign-project'),
        {
            project_id: Number(bulkProjectId.value),
            task_ids: selectedTaskIds.value,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                bulkAssignSubmitting.value = false
            },
            onSuccess: () => {
                closeBulkAssignModal()
                selectedTaskIds.value = []
            },
        },
    )
}

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

const getTaskActions = (task) => [
    {
        name: 'Edit Task',
        callback: () => {
            router.visit(route('task.edit', task))
        },
    },
    {
        name: 'Delete Task',
        callback: () => {
            openConfirmModal('Delete Task', 'Are you sure you want to delete this task?', 'Delete', () => {
                router.delete(route('task.destroy', task.id))
            })
        },
    },
]
</script>
