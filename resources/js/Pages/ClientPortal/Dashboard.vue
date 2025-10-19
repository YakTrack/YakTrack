<template>
    <client-portal-layout>
        <!-- Welcome section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Welcome to your Client Portal</h1>
            <p class="mt-2 text-gray-600">View your projects, tasks, and work sessions.</p>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Projects</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ projects.length }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Tasks</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ totalTasks }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Hours</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ totalBillableHours }}h</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Recent Sessions</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ recentSessions.length }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent sessions -->
        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Recent Sessions</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Your most recent work sessions</p>
            </div>
            <ul class="divide-y divide-gray-200">
                <li v-for="session in recentSessions" :key="session.id" class="px-4 py-4 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-indigo-600 truncate">
                                {{ session.task.name }}
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ session.task.project.name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ formatDate(session.started_at) }} - {{ formatDate(session.ended_at) }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-sm text-gray-500 pr-4">
                            <span v-if="session.session_category" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ session.session_category.name }}
                            </span>
                        </div>
                        <div class="flex-shrink-0 text-sm text-gray-500">
                            <div class="text-sm text-gray-600 font-mono">
                                {{ session.durationForHumans }}
                            </div>
                        </div>
                    </div>
                    <div v-if="session.comment" class="mt-2">
                        <p class="text-sm text-gray-600">{{ session.comment }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </client-portal-layout>
</template>

<script>
import ClientPortalLayout from '@/Shared/ClientPortalLayout.vue'

export default {
    components: {
        'client-portal-layout': ClientPortalLayout,
    },
    props: {
        clientUser: Object,
        projects: Array,
        totalBillableHours: Number,
        recentSessions: Array,
    },
    computed: {
        totalTasks() {
            return this.projects.reduce((total, project) => total + project.tasks.length, 0)
        }
    },
    methods: {
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        },
        getTotalBillableHours(project) {
            let totalHours = 0
            project.tasks.forEach(task => {
                task.sessions.forEach(session => {
                    if (session.durationInSeconds && !isNaN(session.durationInSeconds)) {
                        totalHours += session.durationInSeconds / 3600
                    }
                })
            })
            return Math.round(totalHours * 100) / 100
        }
    },
    mounted() {
        document.title = 'Client Portal Dashboard'
    }
}
</script>
