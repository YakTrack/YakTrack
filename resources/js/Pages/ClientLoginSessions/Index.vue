<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Client Login Sessions'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Client Login Sessions</template>
        <template #top-right-toolbar>
            <button-link :href="route('client-login-sessions.statistics')" color="gray">
                <i class="fa fa-chart-bar text-gray-100 mr-2"></i>
                View Statistics
            </button-link>
        </template>

        <!-- Filters -->
        <div class="card mb-6">
            <div class="card-body">
                <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Client User
                        </label>
                        <select v-model="filters.client_user_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">All Users</option>
                            <option v-for="user in clientUsers" :key="user.id" :value="user.id">
                                {{ user.name }} ({{ user.client.name }})
                            </option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date From
                        </label>
                        <input 
                            v-model="filters.date_from" 
                            type="date" 
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Date To
                        </label>
                        <input 
                            v-model="filters.date_to" 
                            type="date" 
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        />
                    </div>
                    
                    <div class="flex items-end">
                        <div class="flex items-center mb-2">
                            <input 
                                v-model="filters.active_only" 
                                type="checkbox" 
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                            />
                            <label class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Active Sessions Only
                            </label>
                        </div>
                    </div>
                    
                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Apply Filters
                        </button>
                        <button type="button" @click="clearFilters" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Clear Filters
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div v-if="sessions.data.length === 0" class="text-center py-8">
                    <i class="fas fa-sign-in-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No login sessions found</h3>
                    <p class="text-gray-500">No sessions match your current filters.</p>
                </div>

                <div v-else>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Login Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    IP Address
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="session in sessions.data" :key="session.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ session.client_user.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ session.client_user.name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ session.client_user.client.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    {{ formatDate(session.logged_in_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ session.ip_address }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        :class="session.is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'"
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ session.is_active ? 'Active' : 'Ended' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ session.formatted_duration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <actions-dropdown 
                                        :options="getSessionActions(session)" 
                                        direction="left"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="sessions.links" class="mt-6">
                        <nav class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link 
                                    v-if="sessions.prev_page_url" 
                                    :href="sessions.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link 
                                    v-if="sessions.next_page_url" 
                                    :href="sessions.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing
                                        <span class="font-medium">{{ sessions.from }}</span>
                                        to
                                        <span class="font-medium">{{ sessions.to }}</span>
                                        of
                                        <span class="font-medium">{{ sessions.total }}</span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link 
                                            v-for="link in sessions.links" 
                                            :key="link.label"
                                            :href="link.url"
                                            v-html="link.label"
                                            :class="[
                                                'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                                                link.active 
                                                    ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                                link.url === null ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                            ]"
                                        />
                                    </nav>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
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
import { reactive, ref } from 'vue'
import layout from '@/Shared/Layout.vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import actionsDropdown from '@/Shared/ActionsDropdown.vue'
import confirmModal from '@/Shared/ConfirmModal.vue'

const props = defineProps({
    sessions: Object,
    clientUsers: Array,
    filters: Object,
})

const filters = reactive({
    client_user_id: props.filters.client_user_id || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    active_only: props.filters.active_only || false,
})

const showConfirmModal = ref(false)
const confirmModalTitle = ref('')
const confirmModalDescription = ref('')
const confirmModalConfirmText = ref('Confirm')
const pendingAction = ref(null)

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const applyFilters = () => {
    router.get(route('client-login-sessions.index'), filters, {
        preserveState: true,
        preserveScroll: true,
    })
}

const clearFilters = () => {
    filters.client_user_id = ''
    filters.date_from = ''
    filters.date_to = ''
    filters.active_only = false
    
    router.get(route('client-login-sessions.index'), {}, {
        preserveState: true,
        preserveScroll: true,
    })
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

const getSessionActions = (session) => {
    const actions = [
        {
            name: 'View Details',
            callback: () => {
                window.location.href = route('client-login-sessions.show', session.id)
            }
        },
        {
            name: 'View User Sessions',
            callback: () => {
                window.location.href = route('client-users.login-sessions', session.client_user.id)
            }
        }
    ]

    if (session.is_active) {
        actions.push({
            name: 'Logout Session',
            callback: () => {
                openConfirmModal(
                    'Logout Session',
                    'Are you sure you want to logout this session?',
                    'Logout',
                    () => {
                        const form = document.createElement('form')
                        form.method = 'POST'
                        form.action = route('client-login-sessions.logout', session.id)
                        
                        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        const csrfInput = document.createElement('input')
                        csrfInput.type = 'hidden'
                        csrfInput.name = '_token'
                        csrfInput.value = csrfToken
                        form.appendChild(csrfInput)
                        
                        document.body.appendChild(form)
                        form.submit()
                    }
                )
            }
        })
    }

    return actions
}
</script>
