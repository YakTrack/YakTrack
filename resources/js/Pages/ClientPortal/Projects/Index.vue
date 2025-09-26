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
                    <h1 class="text-3xl font-bold text-gray-900">Your Projects</h1>
                    <p class="mt-2 text-gray-600">View all your projects and their associated tasks and sessions.</p>
                </div>

                <!-- Projects grid -->
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="project in projects" :key="project.id" class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-medium text-gray-900 truncate">
                                        {{ project.name }}
                                    </h3>
                                    <p v-if="project.description" class="mt-1 text-sm text-gray-500 truncate">
                                        {{ project.description }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>{{ project.tasks.length }} tasks</span>
                                    <span>{{ getTotalBillableHours(project) }}h</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a :href="route('client-portal.projects.show', project.id)" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty state -->
                <div v-if="projects.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No projects</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any projects yet.</p>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
export default {
    props: {
        clientUser: Object,
        projects: Array,
    },
    methods: {
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
        document.title = 'Projects - Client Portal'
    }
}
</script>
