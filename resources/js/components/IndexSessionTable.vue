<template>
    <div class="space-y-6">
        <!-- Filters Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 transition-all duration-300" v-if="searchParams.get('show-filters') == 'true'">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500">Quick filters:</span>
                </div>
                    </div>
            
            <div class="space-y-4">
                <!-- Quick Filter Buttons -->
                <div class="flex flex-wrap gap-2">
                    <button 
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                        :class="filterPresetIsSelected('thisMonth') 
                            ? 'bg-blue-100 text-blue-700 border border-blue-200' 
                            : 'bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100'"
                        @click="loadFilterPreset('thisMonth')"
                    >
                        This Month
                    </button>
                    <button 
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                        :class="filterPresetIsSelected('lastMonth') 
                            ? 'bg-blue-100 text-blue-700 border border-blue-200' 
                            : 'bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100'"
                        @click="loadFilterPreset('lastMonth')"
                    >
                        Last Month
                    </button>
                    <button 
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                        :class="filterPresetIsSelected('thisWeek')
                            ? 'bg-blue-100 text-blue-700 border border-blue-200' 
                            : 'bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100'"
                        @click="loadFilterPreset('thisWeek')"
                    >
                        This Week
                    </button>
                    <button 
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200"
                        :class="filterPresetIsSelected('lastWeek') 
                            ? 'bg-blue-100 text-blue-700 border border-blue-200' 
                            : 'bg-gray-50 text-gray-700 border border-gray-200 hover:bg-gray-100'"
                        @click="loadFilterPreset('lastWeek')"
                    >
                        Last Week
                    </button>
                </div>

                <!-- Date Range Inputs -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Started After</label>
                        <datetime-input
                            v-model="filters.startedAfter"
                            class="w-full"
                        />
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Started Before</label>
                        <datetime-input
                            v-model="filters.startedBefore"
                            class="w-full"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessions Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden transition-all duration-300" v-if="true">
            <!-- Table Header with Summary (mobile) -->
            <div class="lg:hidden px-6 py-4 bg-gray-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <input 
                                v-model="selectAll" 
                                type="checkbox" 
                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2" 
                            />
                            <span class="text-sm font-medium text-gray-700">Select All</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ sessions.length }} session{{ sessions.length !== 1 ? 's' : '' }}
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right" v-if="selectedSessions.length > 0">
                            <div class="text-sm text-gray-500">Total Duration</div>
                            <div class="text-lg font-mono font-semibold text-gray-900">{{ selectedTotalDuration }}</div>
                        </div>
                            <actions-dropdown :options="actionsDropdown" direction="left"></actions-dropdown>
                    </div>
                </div>
            </div>

            <!-- Sessions Table - Desktop View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full table-fixed">
                    <colgroup>
                        <col class="w-4">
                        <col class="w-[11%]">
                        <col class="w-[13%]">
                        <col class="w-[13%]">
                        <col class="w-[11%]">
                        <col class="w-[8%]">
                        <col class="w-[18%]">
                        <col class="w-[10%]">
                        <col class="w-12">
                    </colgroup>
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <td class="pl-3 pr-4 py-4">
                                <input
                                    v-model="selectAll"
                                    type="checkbox"
                                    class="block w-4 h-4 shrink-0 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                />
                            </td>
                            <td class="pr-4 py-4" colspan="5">
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-700">Select All</span>
                                    <span class="text-sm text-gray-500">
                                        {{ sessions.length }} session{{ sessions.length !== 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </td>
                            <td class="pr-6 py-4 text-right" colspan="2">
                                <div v-if="selectedSessions.length > 0">
                                    <div class="text-sm text-gray-500">Total Duration</div>
                                    <div class="text-lg font-mono font-semibold text-gray-900">{{ selectedTotalDuration }}</div>
                                </div>
                            </td>
                            <td class="pr-6 py-4 text-right">
                                <actions-dropdown :options="actionsDropdown" direction="left"></actions-dropdown>
                            </td>
                        </tr>
                    </thead>
                <tbody v-for="(day, dayIndex) in filteredDays" :key="dayIndex">
                        <!-- Day Header Row -->
                        <tr :class="dayHeaderClasses(day)">
                            <td class="pl-3 pr-4 py-2 text-sm font-medium uppercase tracking-wide">
                                <i class="fas fa-calendar-day ml-0.5" :class="day.is_locked ? 'text-gray-300' : 'text-gray-400'"></i>
                            </td>
                            <td class="pr-5 py-2 text-sm font-medium uppercase tracking-wide" colspan="7">
                                <div class="flex items-center justify-between">
                                    <span>{{ day.sessions[0].localStartedAtDateForHumans }}</span>
                                    <span class="text-sm font-mono font-semibold" :class="day.is_locked ? 'text-gray-400' : 'text-gray-900'">
                                        {{ day.totalDurationForHumans }}
                                    </span>
                                </div>
                            </td>
                            <td class="pr-6 py-2 text-right text-sm font-medium uppercase tracking-wide">
                                <button
                                    type="button"
                                    class="rounded p-1 transition-colors duration-150"
                                    :class="day.is_locked
                                        ? 'text-gray-500 hover:text-gray-600 hover:bg-gray-200'
                                        : 'text-gray-400 hover:text-gray-600 hover:bg-gray-200'"
                                    :title="day.is_locked ? 'Unlock date for editing' : 'Lock date for editing'"
                                    @click="toggleDayLock(day)"
                                >
                                    <i class="fas" :class="day.is_locked ? 'fa-lock' : 'fa-lock-open'" aria-hidden="true"></i>
                                    <span class="sr-only">{{ day.is_locked ? 'Unlock date' : 'Lock date' }}</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Session Rows -->
                        <template v-for="(session, sessionIndex) in day.sessions" :key="session.id">
                            <!-- First Row: Task Name -->
                            <tr
                                :class="[rowClasses(session, day, 'first'), 'session-row transition-colors duration-1500', { 'bg-gray-50': hoveredSessionId === session.id && !isHighlighted(session) && !day.is_locked }]"
                                :data-session-id="session.id"
                                @mouseenter="hoveredSessionId = session.id"
                                @mouseleave="hoveredSessionId = null"
                            >
                                <!-- Checkbox Column -->
                                <td class="pl-3 pr-4 py-2 transition-colors duration-1500" :class="checkboxCellClasses(session, day)" rowspan="2">
                                    <input
                                        type="checkbox"
                                        class="block w-4 h-4 shrink-0 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                        v-model="session.isSelected"
                                        :value="session.id"
                                        :disabled="day.is_locked"
                                    />
                                </td>

                                <!-- Task Name Column -->
                                <td class="pr-4 py-2 min-w-0 transition-colors duration-150" colspan="5">
                                    <div v-if="session.task_id" class="flex items-start">
                                        <Link
                                            class="group flex-1 min-w-0"
                                            :href="route('task.show', session.task_id)"
                                        >
                                            <div class="flex items-center space-x-2 min-w-0">
                                                <span class="text-sm font-semibold truncate transition-colors duration-150" :class="day.is_locked ? 'text-gray-400' : 'text-gray-900 group-hover:text-blue-600'">
                                                    {{ taskPrefix(session.task_name) }}
                                                </span>
                                                <span class="text-sm truncate" :class="day.is_locked ? 'text-gray-400' : 'text-gray-600'">
                                                    {{ taskSuffix(session.task_name) }}
                                                </span>
                                            </div>
                                        </Link>
                                    </div>
                                </td>

                                <!-- Time Range Column -->
                                <td class="pr-6 py-2 text-right transition-colors duration-150" rowspan="2">
                                    <div class="text-sm" :class="day.is_locked ? 'text-gray-400' : 'text-gray-600'">
                                        <div class="flex items-center justify-end space-x-2">
                                            <timestamp class="font-mono" :time="session.started_at"></timestamp>
                                            <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                                            <timestamp class="font-mono" :time="session.ended_at"></timestamp>
                                        </div>
                                    </div>
                                </td>

                                <!-- Duration Column -->
                                <td class="pr-6 py-2 text-right transition-colors duration-150" rowspan="2">
                                    <div class="text-sm font-mono font-semibold" :class="day.is_locked ? 'text-gray-400' : 'text-gray-900'">
                                        <timer :initial-time="session.durationInSeconds" :is-paused="!session.isRunning"></timer>
                                    </div>
                                </td>

                                <!-- Actions Column -->
                                <td class="pr-6 py-2 text-right w-16 transition-colors duration-150" rowspan="2">
                                    <actions-dropdown
                                        v-if="!day.is_locked"
                                        :options="getSessionActions(session)"
                                        direction="left"
                                    ></actions-dropdown>
                                </td>
                            </tr>

                            <!-- Second Row: Context Links -->
                            <tr 
                                :class="[rowClasses(session, day, 'second'), 'border-b border-gray-200 session-row transition-colors duration-1500', { 'bg-gray-50': hoveredSessionId === session.id && !isHighlighted(session) && !day.is_locked }]"
                                :data-session-id="session.id"
                                @mouseenter="hoveredSessionId = session.id"
                                @mouseleave="hoveredSessionId = null"
                            >
                                <!-- Client -->
                                <td class="pr-4 py-1 text-xs transition-colors duration-150">
                                    <Link 
                                        v-if="session.client_id != null" 
                                        class="block truncate transition-colors duration-150" 
                                        :class="day.is_locked ? 'text-gray-400' : 'text-blue-600 hover:text-blue-800'"
                                        :href="route('client.show', session.client_id)"
                                    >
                                        {{ session.client_name }}
                                    </Link>
                                </td>

                                <!-- Project -->
                                <td class="pr-4 py-1 text-xs transition-colors duration-150">
                                    <Link 
                                        v-if="session.project_id != null" 
                                        class="block truncate transition-colors duration-150" 
                                        :class="day.is_locked ? 'text-gray-400' : 'text-indigo-600 hover:text-indigo-800'"
                                        :href="route('project.show', session.project_id)"
                                    >
                                        {{ session.project_name }}
                                    </Link>
                                </td>

                                <!-- Sprint -->
                                <td class="pr-4 py-1 text-xs transition-colors duration-150">
                                    <Link 
                                        v-if="session.sprint_id != null" 
                                        class="block truncate transition-colors duration-150" 
                                        :class="day.is_locked ? 'text-gray-400' : 'text-purple-600 hover:text-purple-800'"
                                        :href="route('sprint.show', session.sprint_id)"
                                    >
                                        {{ session.sprint_name }}
                                    </Link>
                                </td>

                                <!-- Invoice -->
                                <td class="pr-4 py-1 text-xs transition-colors duration-150">
                                    <Link 
                                        v-if="session.invoice_id != null" 
                                        class="block truncate transition-colors duration-150" 
                                        :class="day.is_locked ? 'text-gray-400' : 'text-teal-600 hover:text-teal-800'"
                                        :href="route('invoice.show', session.invoice_id)"
                                    >
                                        {{ session.invoice_number }}
                                    </Link>
                                </td>

                                <!-- Billable -->
                                <td class="pr-4 py-1 text-xs transition-colors duration-150">
                                    <span v-if="session.is_billable" :class="day.is_locked ? 'text-gray-400' : 'text-green-600'">Billable</span>
                                </td>
                            </tr>
                        </template>
                </tbody>
            </table>
            </div>

            <!-- Mobile/Tablet Card View -->
            <div class="lg:hidden">
                <div v-for="(day, dayIndex) in filteredDays" :key="dayIndex" class="mb-6">
                    <!-- Day Header -->
                    <div class="px-4 py-3 border-b border-gray-200" :class="day.is_locked ? 'bg-gray-200' : 'bg-gray-50'">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-calendar-day" :class="day.is_locked ? 'text-gray-300' : 'text-gray-400'"></i>
                                <span class="text-sm font-medium uppercase tracking-wide" :class="day.is_locked ? 'text-gray-400' : 'text-gray-600'">
                                    {{ day.sessions[0].localStartedAtDateForHumans }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-mono font-semibold" :class="day.is_locked ? 'text-gray-400' : 'text-gray-900'">
                                    {{ day.totalDurationForHumans }}
                                </div>
                                <button
                                    type="button"
                                    class="rounded p-1 transition-colors duration-150"
                                    :class="day.is_locked
                                        ? 'text-gray-500 hover:text-gray-600 hover:bg-gray-300'
                                        : 'text-gray-400 hover:text-gray-600 hover:bg-gray-200'"
                                    :title="day.is_locked ? 'Unlock date for editing' : 'Lock date for editing'"
                                    @click="toggleDayLock(day)"
                                >
                                    <i class="fas" :class="day.is_locked ? 'fa-lock' : 'fa-lock-open'" aria-hidden="true"></i>
                                    <span class="sr-only">{{ day.is_locked ? 'Unlock date' : 'Lock date' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Session Cards -->
                    <div class="divide-y divide-gray-100">
                        <div 
                            v-for="(session, sessionIndex) in day.sessions" 
                            :key="session.id" 
                            :class="[rowClasses(session, day, 'card'), 'p-4 transition-colors duration-1500', { 'hover:bg-gray-50': !day.is_locked }]"
                        >
                            <div class="flex items-start justify-between space-x-3">
                                <!-- Checkbox and Main Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start space-x-3">
                                        <input 
                                            type="checkbox" 
                                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 mt-1 flex-shrink-0" 
                                            v-model="session.isSelected" 
                                            :value="session.id"
                                            :disabled="day.is_locked"
                                        />
                                        
                                        <div class="flex-1 min-w-0">
                                            <!-- Task Name -->
                                            <div v-if="session.task_id" class="mb-2">
                                                <Link 
                                                    class="group flex items-start" 
                                                    :href="route('task.show', session.task_id)"
                                                >
                                                    <div class="min-w-0 flex-1">
                                                        <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors duration-150">
                                                            {{ taskPrefix(session.task_name) }}{{ taskSuffix(session.task_name) }}
                                                        </div>
                                                    </div>
                                                </Link>
                                            </div>
                                            
                                            <!-- Context Links -->
                                            <div class="flex flex-wrap gap-1 text-xs mb-2">
                                                <!-- Client -->
                                                <div class="flex-shrink-0">
                                                    <Link 
                                                        v-if="session.client_id != null" 
                                                        class="text-blue-600 hover:text-blue-800 transition-colors duration-150" 
                                                        :href="route('client.show', session.client_id)"
                                                    >
                                                        <span class="truncate max-w-16">{{ session.client_name }}</span>
                                                    </Link>
                                                </div>
                                                
                                                <!-- Project -->
                                                <div class="flex-shrink-0">
                                                    <Link 
                                                        v-if="session.project_id != null" 
                                                        class="text-indigo-600 hover:text-indigo-800 transition-colors duration-150" 
                                                        :href="route('project.show', session.project_id)"
                                                    >
                                                        <span class="truncate max-w-16">{{ session.project_name }}</span>
                                                    </Link>
                                                </div>
                                                
                                                <!-- Sprint -->
                                                <div class="flex-shrink-0">
                                                    <Link 
                                                        v-if="session.sprint_id != null" 
                                                        class="text-purple-600 hover:text-purple-800 transition-colors duration-150" 
                                                        :href="route('sprint.show', session.sprint_id)"
                                                    >
                                                        <span class="truncate max-w-16">{{ session.sprint_name }}</span>
                                                    </Link>
                                                </div>
                                                
                                                <!-- Invoice -->
                                                <div class="flex-shrink-0">
                                                    <Link 
                                                        v-if="session.invoice_id != null" 
                                                        class="text-teal-600 hover:text-teal-800 transition-colors duration-150" 
                                                        :href="route('invoice.show', session.invoice_id)"
                                                    >
                                                        <span class="truncate max-w-16">{{ session.invoice_number }}</span>
                                                    </Link>
                                                </div>
                                                
                                                <!-- Billable -->
                                                <div class="flex-shrink-0">
                                                    <div v-if="session.is_billable" class="text-green-600">
                                                        <span>Billable</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Time and Duration -->
                                            <div class="flex items-center justify-between text-sm">
                                                <div class="text-gray-600">
                                                    <div class="flex items-center space-x-2">
                                                        <timestamp class="font-mono" :time="session.started_at"></timestamp>
                                                        <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                                                        <timestamp class="font-mono" :time="session.ended_at"></timestamp>
                                                    </div>
                                                </div>
                                                <div class="font-mono font-semibold text-gray-900">
                                                    <timer :initial-time="session.durationInSeconds" :is-paused="!session.isRunning"></timer>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="flex-shrink-0">
                                    <actions-dropdown
                                        v-if="!day.is_locked"
                                        :options="getSessionActions(session)"
                                        direction="left"
                                    ></actions-dropdown>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="px-4 lg:px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                    <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <dropdown
                        :options="perPageOptions"
                        :selected-option="selectedPerPageOption"
                        name="Per Page"
                    ></dropdown>
                        <span class="text-sm text-gray-500">
                            Showing {{ (page - 1) * perPage + 1 }} to {{ Math.min(page * perPage, total) }} of {{ total }} sessions
                        </span>
                    </div>
                    <page-selector
                        :total="total"
                        :last-page="lastPage"
                        :page="page"
                        :per-page="perPage"
                        :on-page-select="selectPage"
                    ></page-selector>
                </div>
            </div>
        </div>
        
        <!-- Empty State -->
        <div v-else class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-6 shadow-sm">
                <i class="fas fa-clock text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-3">No sessions found</h3>
            <p class="text-gray-500 max-w-md mx-auto leading-relaxed">
                You haven't created any sessions yet. Start tracking your time to see them appear here.
            </p>
            <div class="mt-6">
                <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    Start Tracking Time
                </button>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <modal
            open-on="confirm-delete-session"
            close-on="close-delete-modal"
            primary-button-text="Delete"
            cancel-button-text="Cancel"
            :primary-danger="true"
            :on-submit="confirmDeleteSession"
        >
            <template #default="{ payload }">
                <div v-if="payload" class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Session</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Are you sure you want to delete this session? This action cannot be undone.
                    </p>
                    <div class="bg-gray-50 p-3 rounded text-sm">
                        <div v-if="payload.task_name" class="mb-2">
                            <strong>Task:</strong> {{ payload.task_name }}
                        </div>
                        <div class="mb-2">
                            <strong>Duration:</strong> {{ payload.durationForHumans }}
                        </div>
                        <div v-if="payload.comment" class="mb-2">
                            <strong>Comment:</strong> {{ payload.comment }}
                        </div>
                    </div>
                </div>
            </template>
        </modal>

        <!-- Bulk delete confirmation -->
        <modal
            open-on="confirm-bulk-delete-sessions"
            close-on="close-bulk-delete-modal"
            primary-button-text="Delete"
            cancel-button-text="Cancel"
            :primary-danger="true"
            :on-submit="confirmBulkDeleteSessions"
        >
            <template #default="{ payload }">
                <div v-if="payload && payload.length" class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Delete selected sessions</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        Are you sure you want to delete {{ payload.length }} session{{ payload.length !== 1 ? 's' : '' }}? This action cannot be undone.
                    </p>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/vue3'
    import dropdown from '@/Shared/Dropdown.vue';
    import actionsDropdown from '@/Shared/ActionsDropdown.vue';
    import deleteButton from '@/Shared/DeleteButton.vue';
    import timestamp from '@/Shared/Timestamp.vue';
    import modal from '@/components/LegacyModal.vue';
    import timer from '@/components/Timer.vue';
    import dateTime from '@/filters/DateTime.js';
    import datetimeInput from '@/components/DatetimeInput.vue';
    import pageSelector from '@/components/PageSelector.vue';
    import urlParser from '@/UrlParser.js';
    import searchParams from '@/SearchParams';
    import dayjs from 'dayjs';

    const DATE_FORMAT = "yyyy'-'MM'-'dd HH':'mm':'ss";

    export default {
        props: [
            'invoices',
            'sprints',
            'thirdPartyApplications',
            'days',
            'page',
            'perPage',
            'total',
            'lastPage',
            'onChangeSelectedSessionIds',
            'onSplitSession',
            'onEditSession',
            'onAddSessionBefore',
            'onAddSessionAfter',
            'highlightedSessionId',
        ],
        components: {
            Link,
            deleteButton: deleteButton,
            dropdown: dropdown,
            actionsDropdown: actionsDropdown,
            modal: modal,
            timer: timer,
            timestamp: timestamp,
            datetimeInput: datetimeInput,
            pageSelector: pageSelector,
        },
        data() {
            return {
                dayjs: dayjs,
                selectAll: false,
                selectedInvoiceId: null,
                sessionToDelete: null,
                hoveredSessionId: null,
                dateFormat: DATE_FORMAT,
                filters: {
                    startedAfter: null,
                    startedBefore: null,
                },
                dateTime: dateTime,
                urlParser: urlParser,
                searchParams: searchParams,
                filterPresets: {
                    thisWeek: {
                        startedAfter: dateTime.startOfWeek(new Date()),
                        startedBefore: dateTime.endOfWeek(new Date()),
                    },
                    lastWeek: {
                        startedAfter: dateTime.startOfLastWeek(new Date()),
                        startedBefore: dateTime.endOfLastWeek(new Date()),
                    },
                    thisMonth: {
                        startedAfter: dateTime.startOfMonth(new Date()),
                        startedBefore: dateTime.endOfMonth(new Date()),
                    },
                    lastMonth: {
                        startedAfter: dateTime.startOfLastMonth(new Date()),
                        startedBefore: dateTime.endOfLastMonth(new Date()),
                    },
                },
            };
        },
        computed: {
            selectedSessions() {
                return this.sessions.filter(function (session) {
                    return session.isSelected;
                });
            },
            editableSessions() {
                return this.sessions.filter((session) => !this.isSessionLocked(session));
            },
            selectedSessionIds() {
                return this.selectedSessions.map((session) => session.id);
            },
            sessions() {
                return this.days.reduce((acculumatedSessions, day) => [].concat(acculumatedSessions, day.sessions), []);
            },
            filteredDays() {
                return this.days.filter(day => day.sessions.length > 0);
            },
            selectedTotalDuration() {
                return this.dateTime.durationForHumans(this.selectedSessions.reduce(function (accumulator, session) {
                    return session.durationInSeconds + accumulator
                }, 0));
            },
            perPageOptions() {
                return [
                    {
                        name: '50 per page',
                        event: {
                            name: 'set-per-page',
                            args: 50,
                        },
                    },
                    {
                        name: '100 per page',
                        event: {
                            name: 'set-per-page',
                            args: 100,
                        },
                    },
                    {
                        name: '200 per page',
                        event: {
                            name: 'set-per-page',
                            args: 200,
                        },
                    },
                    {
                        name: '500 per page',
                        event: {
                            name: 'set-per-page',
                            args: 500,
                        },
                    },
                    {
                        name: '1000 per page',
                        event: {
                            name: 'set-per-page',
                            args: 1000,
                        },
                    },
                ];
            },
            selectedPerPageOption() {
                return this.perPageOptions.find(option =>{
                    return Number.parseInt(option.name) == searchParams.get('per-page');
                });
            },
            actionsDropdown() {
                return [
                    {
                        name: 'Link to invoice',
                        event: 'sessions.link-to-invoice',
                    },
                    {
                        name: 'Link to sprint',
                        event: 'sessions.link-to-sprint',
                    },
                    {
                        name: 'Link to task',
                        event: 'sessions.link-to-task',
                    },
                    {
                        name: 'Mark as billable',
                        event: 'sessions.mark-as-billable',
                    },
                    {
                        name: 'Mark as non-billable',
                        event: 'sessions.mark-as-non-billable',
                    },
                    {
                        name: 'Lock all',
                        callback: () => this.bulkLockDates(),
                    },
                    {
                        name: 'Unlock all',
                        callback: () => this.bulkUnlockDates(),
                    },
                    {
                        name: 'Delete selected',
                        callback: () => {
                            if (this.selectedSessions.length === 0) {
                                return;
                            }
                            events.emit('confirm-bulk-delete-sessions', this.selectedSessions);
                        },
                    },
                ];
            },
        },
        mounted() {
            this.setDateTimeFilters(
                this.dateTime.toInputFormat(this.dateTime.fromSearchParam(searchParams.get('started-after'))),
                this.dateTime.toInputFormat(this.dateTime.fromSearchParam(searchParams.get('started-before'))),
                false
            );

            this._onSetPerPage = (perPage) => {
                this.$inertia.visit(this.urlParser.current({
                    perPage: perPage,
                }), {
                    replace: true,
                    preserveScroll: true,
                });
            };
            this._onMarkAsBillable = () => this.updateSelectedSessions({ is_billable: 1 });
            this._onMarkAsNonBillable = () => this.updateSelectedSessions({ is_billable: 0 });
            this._onStopSession = (session) => this.stopSession(session);
            this._onContinueSession = (session) => this.continueSession(session);
            this._onSplitSession = (session) => this.splitSession(session);
            this._onEditSession = (session) => {
                if (this.onEditSession) {
                    this.onEditSession(session);
                }
            };
            this._onAddSessionBefore = (session) => {
                if (this.onAddSessionBefore) {
                    this.onAddSessionBefore(session);
                }
            };
            this._onAddSessionAfter = (session) => {
                if (this.onAddSessionAfter) {
                    this.onAddSessionAfter(session);
                }
            };
            this._onConfirmDeleteSession = (session) => {
                this.sessionToDelete = session;
            };

            events.on('set-per-page', this._onSetPerPage);
            events.on('sessions.mark-as-billable', this._onMarkAsBillable);
            events.on('sessions.mark-as-non-billable', this._onMarkAsNonBillable);
            events.on('stop-session', this._onStopSession);
            events.on('continue-session', this._onContinueSession);
            events.on('split-session', this._onSplitSession);
            events.on('edit-session', this._onEditSession);
            events.on('add-session-before', this._onAddSessionBefore);
            events.on('add-session-after', this._onAddSessionAfter);
            events.on('confirm-delete-session', this._onConfirmDeleteSession);
        },
        beforeUnmount() {
            events.off('set-per-page', this._onSetPerPage);
            events.off('sessions.mark-as-billable', this._onMarkAsBillable);
            events.off('sessions.mark-as-non-billable', this._onMarkAsNonBillable);
            events.off('stop-session', this._onStopSession);
            events.off('continue-session', this._onContinueSession);
            events.off('split-session', this._onSplitSession);
            events.off('edit-session', this._onEditSession);
            events.off('add-session-before', this._onAddSessionBefore);
            events.off('add-session-after', this._onAddSessionAfter);
            events.off('confirm-delete-session', this._onConfirmDeleteSession);
        },
        methods: {
            loadFilterPreset(presetKey) {
                this.setDateTimeFilters(
                    this.filterPresets[presetKey].startedAfter,
                    this.filterPresets[presetKey].startedBefore
                );
            },
            filterPresetIsSelected(presetKey) {
                const preset = this.filterPresets[presetKey];
                return this.filters.startedAfter == preset.startedAfter && this.filters.startedBefore == preset.startedBefore;
            },
            deleteSession(session) {
                this.$inertia.delete(session.destroyUrl);
            },
            stopSession(session) {
                this.$inertia.post(route('session.stop', session.id));
            },
            updateSelectedSessions(payload) {
               const editableSelectedSessions = this.selectedSessions.filter((session) => !this.isSessionLocked(session));

               if (editableSelectedSessions.length === 0) {
                   return;
               }

               this.$inertia.patch(
                   route('sessions.update'),
                   {
                        sessions: editableSelectedSessions.reduce((sessions, session) => {
                            sessions[session.id] = payload

                            return sessions
                        }, {})
                   }
                )
            },
            getSessions() {
                this.$inertia.get(`json/session`, searchParams)
                    .then(response => {
                    this.$set(this, 'days', response.data.days.map(day => {
                        day.sessions = day.sessions.map(session => {
                            session.isSelected = false;
                            return session;
                        });
                        return day;
                    }));
                    this.$set(this, 'total', response.data.total);
                    this.$set(this, 'lastPage', response.data.lastPage);
                });
            },
            toggleAllCheckboxes(event) {
                this.sessions.forEach((session) => {
                    session.isSelected = event.target.isChecked;
                });
            },
            isHighlighted(session) {
                return this.highlightedSessionId === session.id;
            },
            checkboxCellClasses(session, day) {
                if (day?.is_locked) {
                    return 'border-l border-transparent';
                }

                if (this.isHighlighted(session)) {
                    return 'border-l-4 border-l-amber-300';
                }

                return 'border-l border-transparent';
            },
            rowClasses(session, day, rowPart = 'card') {
                if (day?.is_locked) {
                    if (rowPart === 'first' || rowPart === 'second') {
                        return 'bg-gray-100 text-gray-400';
                    }

                    return 'bg-gray-100 text-gray-400';
                }

                if (this.isHighlighted(session)) {
                    if (rowPart === 'first' || rowPart === 'second') {
                        return 'bg-amber-50';
                    }

                    return 'bg-amber-50 ring-1 ring-inset ring-amber-100';
                }

                if (session.isRunning) {
                    return 'bg-green-50 border-l-4 border-l-green-400';
                }

                if (session.isSelected) {
                    return 'bg-blue-50 border-l-4 border-l-blue-400';
                }

                return '';
            },
            continueSession(session) {
                this.$inertia.post(`session/${session.id}/continue`)
            },
            setDateTimeFilters(startedAfter, startedBefore, reload = true) {
                this.setStartedAfterFilter(startedAfter);
                this.setStartedBeforeFilter(startedBefore);

                if (!reload) {
                    return;
                }

                this.$inertia.visit(this.urlParser.current({
                    startedAfter: this.filters.startedAfter,
                    startedBefore: this.filters.startedBefore,
                }), {
                    preserveScroll: true,
                });

            },
            setStartedAfterFilter(startedAfter) {
                this.filters.startedAfter = startedAfter;
            },
            setStartedBeforeFilter(startedBefore) {
                this.filters.startedBefore = startedBefore;
            },
            selectPage(page) {
                this.$inertia.visit(this.urlParser.current({page: page}), {
                    replace: true,
                    preserveScroll: true,
                });
            },
            taskPrefix(name) {
                return name.split(':')[0];
            },
            taskSuffix(name) {
                let suffix = name.split(':')[1];

                return suffix ? ':' + suffix : '';
            },
            splitSession(session) {
                if (this.onSplitSession) {
                    this.onSplitSession(session);
                }
            },
            getSessionActions(session) {
                const actions = [];
                
                if (session.isRunning) {
                    actions.push({
                        name: 'Stop Session',
                        event: {
                            name: 'stop-session',
                            args: session
                        }
                    });
                } else {
                    actions.push({
                        name: 'Continue Session',
                        event: {
                            name: 'continue-session',
                            args: session
                        }
                    });
                    
                    if (session.ended_at) {
                        actions.push({
                            name: 'Split Session',
                            event: {
                                name: 'split-session',
                                args: session
                            }
                        });
                    }
                }

                if (session.can_add_session_before) {
                    actions.push({
                        name: 'Add Session Before',
                        event: {
                            name: 'add-session-before',
                            args: session
                        }
                    });
                }

                if (session.can_add_session_after) {
                    actions.push({
                        name: 'Add Session After',
                        event: {
                            name: 'add-session-after',
                            args: session
                        }
                    });
                }

                actions.push({
                    name: 'Edit Session',
                    event: {
                        name: 'edit-session',
                        args: session
                    }
                });
                
                actions.push({
                    name: 'Delete Session',
                    event: {
                        name: 'confirm-delete-session',
                        args: session
                    }
                });
                
                return actions;
            },
            confirmDeleteSession(session) {
                if (session) {
                    this.$inertia.delete(route('session.destroy', session.id));
                }
                events.emit('close-delete-modal');
            },
            confirmBulkDeleteSessions(sessions) {
                if (sessions && sessions.length > 0) {
                    const editableSessions = sessions.filter((session) => !this.isSessionLocked(session));

                    if (editableSessions.length === 0) {
                        return;
                    }

                    this.$inertia.post(route('sessions.destroy-many'), {
                        session_ids: editableSessions.map((session) => session.id),
                    });
                }
                events.emit('close-bulk-delete-modal');
            },
            dayHeaderClasses(day) {
                return day.is_locked
                    ? 'bg-gray-200 border-b border-gray-300 text-gray-400'
                    : 'bg-gray-100 border-b border-gray-300 text-gray-600';
            },
            isSessionLocked(session) {
                return this.days.some((day) => day.is_locked && day.sessions.some((daySession) => daySession.id === session.id));
            },
            toggleDayLock(day) {
                this.$inertia.post(route('session-dates.toggle-lock', day.date), {}, {
                    preserveScroll: true,
                });
            },
            bulkLockDates() {
                const dates = this.filteredDays.map((day) => day.date);

                if (dates.length === 0) {
                    return;
                }

                this.$inertia.post(route('session-dates.lock-many'), { dates }, {
                    preserveScroll: true,
                });
            },
            bulkUnlockDates() {
                const dates = this.filteredDays.map((day) => day.date);

                if (dates.length === 0) {
                    return;
                }

                this.$inertia.post(route('session-dates.unlock-many'), { dates }, {
                    preserveScroll: true,
                });
            },
        },
        watch: {
            selectAll(newValue) {
                this.sessions.forEach((session) => {
                    if (!this.isSessionLocked(session)) {
                        session.isSelected = newValue;
                    }
                });
            },
            filters: {
                deep: true,
                handler(newValue) {
                }
            },
            selectedSessions(newValue) {
              if (this.onChangeSelectedSessionIds) {
                (this.onChangeSelectedSessionIds)(newValue.map(session => session.id))
              }
            }            
        },
    }
</script>

