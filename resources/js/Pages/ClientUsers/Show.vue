<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Client Users', url: route('client-users.index')},
                    {title: clientUser.name},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ clientUser.name }}</template>
        <template #top-right-toolbar>
            <button-link :href="route('client-users.edit', clientUser.id)" color="blue">
                <i class="fa fa-edit text-blue-100 mr-2"></i>
                Edit User
            </button-link>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Details -->
            <div class="lg:col-span-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">User Details</h3>
                    </div>
                    <div class="card-body">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ clientUser.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ clientUser.email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Client</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ clientUser.client.name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1">
                                    <span 
                                        :class="clientUser.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                    >
                                        {{ clientUser.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(clientUser.created_at) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ formatDate(clientUser.updated_at) }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Recent Login Sessions -->
                <div class="card mt-6">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Recent Login Sessions</h3>
                            <button-link :href="route('client-users.login-sessions', clientUser.id)" color="gray" size="sm">
                                View All Sessions
                            </button-link>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="clientUser.login_sessions && clientUser.login_sessions.length > 0">
                            <div class="overflow-hidden">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
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
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr v-for="session in clientUser.login_sessions" :key="session.id">
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
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <i class="fas fa-sign-in-alt text-gray-400 text-4xl mb-4"></i>
                            <p class="text-gray-500">No login sessions found</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Sidebar -->
            <div class="lg:col-span-1">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Actions</h3>
                    </div>
                    <div class="card-body space-y-4">
                        <button-link :href="route('client-users.edit', clientUser.id)" color="blue" class="w-full">
                            <i class="fa fa-edit mr-2"></i>
                            Edit User
                        </button-link>
                        
                        <button-link :href="route('client-users.login-sessions', clientUser.id)" color="gray" class="w-full">
                            <i class="fa fa-history mr-2"></i>
                            View Login Sessions
                        </button-link>

                        <button 
                            v-if="clientUser.is_active"
                            @click="logoutAllSessions"
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            <i class="fa fa-sign-out-alt mr-2"></i>
                            Logout All Sessions
                        </button>

                        <button 
                            @click="deleteUser"
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                        >
                            <i class="fa fa-trash mr-2"></i>
                            Delete User
                        </button>
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
import { ref } from 'vue'
import layout from '@/Shared/Layout.vue'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import buttonLink from '@/Shared/ButtonLink.vue'
import confirmModal from '@/Shared/ConfirmModal.vue'

const props = defineProps({
    clientUser: Object,
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

const logoutAllSessions = () => {
    openConfirmModal(
        'Logout All Sessions',
        'Are you sure you want to logout all active sessions for this user?',
        'Logout All',
        () => {
            const form = document.createElement('form')
            form.method = 'POST'
            form.action = route('client-users.logout-all-sessions', props.clientUser.id)
            
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

const deleteUser = () => {
    openConfirmModal(
        'Delete Client User',
        'Are you sure you want to delete this client user? This action cannot be undone.',
        'Delete',
        () => {
            router.delete(route('client-users.destroy', props.clientUser.id))
        }
    )
}
</script>
