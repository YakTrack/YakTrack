<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a :href="route('client-portal.dashboard')" class="text-2xl font-bold text-gray-900">
                                <b>Y</b>ak<b>T</b>rack <span class="text-sm text-gray-600">Client Portal</span>
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a :href="route('client-portal.dashboard')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Dashboard
                            </a>
                            <a :href="route('client-portal.projects.index')" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Projects
                            </a>
                            <a :href="route('client-portal.tasks.index')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Tasks
                            </a>
                            <a :href="route('client-portal.sessions.index')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Sessions
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <span class="text-sm text-gray-700">Welcome, {{ clientUser.name }}</span>
                        </div>
                        <div class="ml-4">
                            <form :action="route('client-portal.logout')" method="POST" class="inline">
                                <input type="hidden" name="_token" :value="$page.props.csrf_token">
                                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ project.name }}</h1>
                            <p v-if="project.description" class="mt-2 text-gray-600">{{ project.description }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a :href="route('client-portal.projects.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Back to Projects
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Project stats -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
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
                                        <dd class="text-lg font-medium text-gray-900">{{ project.tasks.length }}</dd>
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
                                        <dt class="text-sm font-medium text-gray-500 truncate">Billable Hours</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ getTotalBillableHours() }}h</dd>
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
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Sessions</dt>
                                        <dd class="text-lg font-medium text-gray-900">{{ getTotalSessions() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tasks -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Tasks</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">All tasks in this project with their billable sessions</p>
                    </div>
                    <ul class="divide-y divide-gray-200">
                        <li v-for="task in project.tasks" :key="task.id" class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-indigo-600 truncate">
                                        {{ task.name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ task.sessions.length }} billable sessions • {{ getTaskBillableHours(task) }}h total
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <a :href="route('client-portal.tasks.show', task.id)" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        View Task
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Recent sessions for this task -->
                            <div v-if="task.sessions.length > 0" class="mt-3">
                                <div class="text-xs text-gray-500 mb-2">Recent sessions:</div>
                                <div class="space-y-2">
                                    <div v-for="session in task.sessions.slice(0, 3)" :key="session.id" class="bg-gray-50 rounded-md p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-900">{{ formatDate(session.started_at) }}</p>
                                                <p class="text-sm text-gray-500">{{ session.duration_for_humans }}</p>
                                                <p v-if="session.notes" class="text-sm text-gray-600 mt-1">{{ session.notes }}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span v-if="session.session_category" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ session.session_category.name }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Empty state -->
                <div v-if="project.tasks.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks</h3>
                    <p class="mt-1 text-sm text-gray-500">This project doesn't have any tasks yet.</p>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
export default {
    props: {
        clientUser: Object,
        project: Object,
    },
    methods: {
        getTotalBillableHours() {
            let totalHours = 0
            this.project.tasks.forEach(task => {
                task.sessions.forEach(session => {
                    totalHours += session.duration_in_seconds / 3600
                })
            })
            return Math.round(totalHours * 100) / 100
        },
        getTotalSessions() {
            return this.project.tasks.reduce((total, task) => total + task.sessions.length, 0)
        },
        getTaskBillableHours(task) {
            let totalHours = 0
            task.sessions.forEach(session => {
                totalHours += session.duration_in_seconds / 3600
            })
            return Math.round(totalHours * 100) / 100
        },
        formatDate(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            })
        }
    },
    mounted() {
        document.title = `${this.project.name} - Client Portal`
    }
}
</script>
