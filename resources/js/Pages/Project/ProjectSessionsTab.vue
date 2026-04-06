<template>
    <div
        class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900"
    >
        <data-table-toolbar
            v-model="filters.q"
            search-placeholder="Search task, comment, sprint, or category…"
            :show-clear="hasActiveFilters"
            @search="onSearchInput"
            @clear="clearFilters"
        >
            <template #filters>
                <div class="min-w-[200px]">
                    <label
                        for="project-session-sprint"
                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400"
                    >
                        Sprint
                    </label>
                    <select
                        id="project-session-sprint"
                        v-model="filters.sprint_id"
                        class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        @change="applyFilters"
                    >
                        <option value="">All sprints</option>
                        <option value="none">No sprint</option>
                        <option v-for="s in sessionSprintFilters" :key="s.id" :value="String(s.id)">
                            {{ s.name }}
                        </option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label
                        for="project-session-billable"
                        class="mb-1 block text-xs font-medium text-gray-600 dark:text-gray-400"
                    >
                        Billable
                    </label>
                    <select
                        id="project-session-billable"
                        v-model="filters.billable"
                        class="block w-full rounded-lg border border-gray-300 bg-white py-2 pl-3 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        @change="applyFilters"
                    >
                        <option value="">All</option>
                        <option value="yes">Billable</option>
                        <option value="no">Non-billable</option>
                    </select>
                </div>
            </template>
        </data-table-toolbar>

        <div v-if="sessions.data.length" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
                <thead class="bg-gray-50/90 dark:bg-gray-800/60">
                    <tr>
                        <sortable-column-header
                            label="Date"
                            column="ended_at"
                            :sort="sort"
                            :direction="direction"
                            @sort="toggleSort($event)"
                        />
                        <sortable-column-header
                            label="Task"
                            column="task"
                            :sort="sort"
                            :direction="direction"
                            @sort="toggleSort($event)"
                        />
                        <sortable-column-header
                            label="Sprint"
                            column="sprint"
                            :sort="sort"
                            :direction="direction"
                            @sort="toggleSort($event)"
                        />
                        <sortable-column-header
                            label="Category"
                            column="category"
                            :sort="sort"
                            :direction="direction"
                            @sort="toggleSort($event)"
                        />
                        <sortable-column-header
                            label="Duration"
                            column="duration"
                            :sort="sort"
                            :direction="direction"
                            @sort="toggleSort($event)"
                        />
                        <sortable-column-header
                            label="Billable"
                            column="billable"
                            :sort="sort"
                            :direction="direction"
                            @sort="toggleSort($event)"
                        />
                        <th
                            class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400"
                            scope="col"
                        >
                            Comment
                        </th>
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
                        v-for="session in sessions.data"
                        :key="session.id"
                        class="transition-colors hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
                    >
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                            {{ formatDate(session.ended_at) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                            <Link
                                v-if="session.task"
                                :href="route('task.show', session.task.id)"
                                class="font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300"
                            >
                                {{ session.task.name }}
                            </Link>
                            <span v-else class="text-gray-400 dark:text-gray-500">—</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            <Link
                                v-if="session.sprint"
                                :href="route('sprint.show', session.sprint.id)"
                                class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                            >
                                {{ session.sprint.name }}
                            </Link>
                            <span v-else class="text-gray-400 dark:text-gray-500">—</span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ session.session_category ? session.session_category.name : '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ formatDuration(session.started_at, session.ended_at) }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm">
                            <span
                                v-if="session.is_billable"
                                class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-800 dark:bg-green-900/40 dark:text-green-200"
                            >
                                Yes
                            </span>
                            <span
                                v-else
                                class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-200"
                            >
                                No
                            </span>
                        </td>
                        <td class="max-w-xs truncate px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                            {{ session.comment || '—' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                            <Link
                                :href="route('session.edit', session.id)"
                                class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                            >
                                Edit
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-else class="px-6 py-16 text-center">
            <div
                class="mx-auto mb-4 flex size-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800"
            >
                <i class="fa fa-clock text-2xl text-gray-400 dark:text-gray-500" aria-hidden="true"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{ hasActiveFilters ? 'No matching sessions' : 'No sessions yet' }}
            </h3>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                {{
                    hasActiveFilters
                        ? 'Try adjusting your search or filters.'
                        : 'Sessions will appear here as work is tracked on this project.'
                }}
            </p>
            <button
                v-if="hasActiveFilters"
                type="button"
                class="mt-6 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400"
                @click="clearFilters"
            >
                Clear filters
            </button>
        </div>

        <inertia-table-pagination
            v-if="sessions.data.length"
            :paginator="sessions"
            :per-page="perPage"
            @per-page="setPerPage"
        />
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import DataTableToolbar from '@/components/data-table/DataTableToolbar.vue'
import InertiaTablePagination from '@/components/data-table/InertiaTablePagination.vue'
import SortableColumnHeader from '@/components/data-table/SortableColumnHeader.vue'
import { useInertiaTable } from '@/composables/useInertiaTable'

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
    sessions: {
        type: Object,
        required: true,
    },
    sessionSprintFilters: {
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
    routeName: 'project.show',
    tableKey: 'sessionsTable',
    routeBinding: () => props.project.id,
    pageQueryKey: 'sessions_page',
    ignoreFilterKeys: ['tab'],
    filterDefaults: {
        tab: 'sessions',
        q: '',
        sprint_id: '',
        billable: '',
    },
    sortDefaults: { sort: 'ended_at', direction: 'desc' },
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}

const formatDuration = (startedAt, endedAt) => {
    const start = new Date(startedAt)
    const end = new Date(endedAt)
    const diffMs = end - start
    const diffHours = Math.floor(diffMs / (1000 * 60 * 60))
    const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60))

    if (diffHours > 0) {
        return `${diffHours}h ${diffMinutes}m`
    }

    return `${diffMinutes}m`
}
</script>
