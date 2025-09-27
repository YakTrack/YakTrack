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
                            <a :href="route('client-portal.projects.index')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Projects
                            </a>
                            <a :href="route('client-portal.tasks.index')" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
                    <h1 class="text-3xl font-bold text-gray-900">Your Tasks</h1>
                    <p class="mt-2 text-gray-600">View all your tasks and their work sessions.</p>
                </div>

                <!-- Tasks list -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="task in tasks" :key="task.id" class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1">
                                            <a :href="route('client-portal.tasks.show', task.id)" class="text-base font-medium text-blue-800 hover:underline">
                                                {{ task.name }}
                                            </a>
                                        </div>
                                        <div>
                                            <span v-if="task.task_status" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium whitespace-nowrap" :style="{ backgroundColor: task.task_status.color + '20', color: task.task_status.color }">
                                                {{ task.task_status.name }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ task.project.name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ task.sessions.length }} sessions • {{ getTaskBillableHours(task) }}h total
                                    </p>
                                </div>
                            </div>

                            <!-- Recent sessions for this task -->
                            <div v-if="task.sessions.length > 0" class="mt-3">
                                <div class="text-xs text-gray-500 mb-2">Recent sessions:</div>
                                <div class="space-y-2">
                                    <div v-for="session in task.sessions.slice(0, 2)" :key="session.id" class="bg-gray-50 rounded-md p-3">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-700 flex">
                                                    <div>{{ formatDate(session.started_at) }}</div>
                                                    <div class="px-2"> - </div>
                                                    <div>{{ session.ended_at ? formatDate(session.ended_at) : 'Now' }}</div>
                                                </div>
                                                <p v-if="session.comment" class="text-sm text-gray-600 mt-1">{{ session.comment }}</p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                <span v-if="session.session_category" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ session.session_category.name }}
                                                </span>
                                            </div>
                                            <div class="flex-shrink-0 px-4">
                                                <p class="text-sm text-gray-500">
                                                    {{ session.durationForHumans }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Empty state -->
                <div v-if="tasks.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No tasks</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any tasks yet.</p>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
export default {
    props: {
        clientUser: Object,
        tasks: Array,
    },
    methods: {
        getTaskBillableHours(task) {
            let totalHours = 0
            task.sessions.forEach(session => {
                if (session.durationInSeconds && !isNaN(session.durationInSeconds)) {
                    totalHours += session.durationInSeconds / 3600
                }
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
        document.title = 'Tasks - Client Portal'
    }
}
</script>
