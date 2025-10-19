<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Client Users'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Client Users</template>
        <template #top-right-toolbar>
            <button-link :href="route('client-users.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Client User
            </button-link>
        </template>

        <div class="card">
            <div class="card-body">
                <div v-if="clientUsers.data.length === 0" class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No client users found</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first client user.</p>
                    <button-link :href="route('client-users.create')" color="blue">
                        Create Client User
                    </button-link>
                </div>

                <div v-else>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Client
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Last Login
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            <tr v-for="clientUser in clientUsers.data" :key="clientUser.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-sm font-medium text-white">
                                                    {{ clientUser.name.charAt(0).toUpperCase() }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ clientUser.name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ clientUser.email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ clientUser.client.name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        :class="clientUser.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ clientUser.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <div v-if="clientUser.login_sessions && clientUser.login_sessions.length > 0">
                                        {{ formatDate(clientUser.login_sessions[0].logged_in_at) }}
                                    </div>
                                    <div v-else class="text-gray-400 italic">
                                        Never logged in
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <actions-dropdown 
                                        :options="getClientUserActions(clientUser)" 
                                        direction="left"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="clientUsers.links" class="mt-6">
                        <nav class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link 
                                    v-if="clientUsers.prev_page_url" 
                                    :href="clientUsers.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Previous
                                </Link>
                                <Link 
                                    v-if="clientUsers.next_page_url" 
                                    :href="clientUsers.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing
                                        <span class="font-medium">{{ clientUsers.from }}</span>
                                        to
                                        <span class="font-medium">{{ clientUsers.to }}</span>
                                        of
                                        <span class="font-medium">{{ clientUsers.total }}</span>
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link 
                                            v-for="link in clientUsers.links" 
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
    </layout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import layout from '@/Shared/Layout.vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import actionsDropdown from '@/Shared/ActionsDropdown.vue'

const props = defineProps({
    clientUsers: Object,
})

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

const getClientUserActions = (clientUser) => {
    const actions = [
        {
            name: 'View',
            callback: () => {
                window.location.href = route('client-users.show', clientUser.id)
            }
        },
        {
            name: 'Edit',
            callback: () => {
                window.location.href = route('client-users.edit', clientUser.id)
            }
        },
        {
            name: 'Login Sessions',
            callback: () => {
                window.location.href = route('client-users.login-sessions', clientUser.id)
            }
        }
    ]

    if (clientUser.is_active) {
        actions.push({
            name: 'Logout All Sessions',
            callback: () => {
                if (confirm('Are you sure you want to logout all active sessions for this user?')) {
                    const form = document.createElement('form')
                    form.method = 'POST'
                    form.action = route('client-users.logout-all-sessions', clientUser.id)
                    
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    const csrfInput = document.createElement('input')
                    csrfInput.type = 'hidden'
                    csrfInput.name = '_token'
                    csrfInput.value = csrfToken
                    form.appendChild(csrfInput)
                    
                    document.body.appendChild(form)
                    form.submit()
                }
            }
        })
    }

    actions.push({
        name: 'Delete',
        callback: () => {
            if (confirm('Are you sure you want to delete this client user? This action cannot be undone.')) {
                const form = document.createElement('form')
                form.method = 'POST'
                form.action = route('client-users.destroy', clientUser.id)
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                const csrfInput = document.createElement('input')
                csrfInput.type = 'hidden'
                csrfInput.name = '_token'
                csrfInput.value = csrfToken
                form.appendChild(csrfInput)
                
                const methodInput = document.createElement('input')
                methodInput.type = 'hidden'
                methodInput.name = '_method'
                methodInput.value = 'DELETE'
                form.appendChild(methodInput)
                
                document.body.appendChild(form)
                form.submit()
            }
        }
    })

    return actions
}
</script>
