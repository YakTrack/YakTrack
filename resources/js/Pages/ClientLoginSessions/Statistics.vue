<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Client Login Sessions', url: route('client-login-sessions.index')},
                    {title: 'Statistics'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Login Session Statistics</template>
        <template #top-right-toolbar>
            <button-link :href="route('client-login-sessions.index')" color="gray">
                <i class="fa fa-list text-gray-100 mr-2"></i>
                View All Sessions
            </button-link>
        </template>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-sign-in-alt text-blue-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sessions</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.total_sessions }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-circle text-green-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Active Sessions</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.active_sessions }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-calendar-day text-purple-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Sessions Today</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.sessions_today }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-users text-orange-500 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Unique Users Today</p>
                            <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.unique_users_today }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Weekly and Monthly Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">This Week</h3>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sessions</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.sessions_this_week }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unique Users</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.unique_users_this_week }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">This Month</h3>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Sessions</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.sessions_this_month }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Unique Users</dt>
                            <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ stats.unique_users_this_month }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Top Active Users -->
        <div class="card">
            <div class="card-header">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Most Active Users (Last 7 Days)</h3>
            </div>
            <div class="card-body">
                <div v-if="topActiveUsers.length === 0" class="text-center py-8">
                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No activity data available</p>
                </div>
                <div v-else>
                    <div class="overflow-hidden">
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
                                        Sessions (7 days)
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="user in topActiveUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-white">
                                                        {{ user.name.charAt(0).toUpperCase() }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ user.name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ user.email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ user.client.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ user.recent_sessions_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button-link :href="route('client-users.login-sessions', user.id)" color="blue" size="sm">
                                            View Sessions
                                        </button-link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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
import buttonLink from '@/Shared/ButtonLink.vue'

const props = defineProps({
    stats: Object,
    topActiveUsers: Array,
})
</script>
