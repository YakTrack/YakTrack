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
                            <a :href="route('client-portal.tasks.index')" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                                Tasks
                            </a>
                            <a :href="route('client-portal.sessions.index')" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
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
                    <h1 class="text-3xl font-bold text-gray-900"> Work Sessions </h1>
                    <p class="mt-2 text-gray-600">View all work sessions being done across your projects.</p>
                </div>

                <!-- Sessions list -->
                <div class="bg-white shadow overflow-hidden sm:rounded-md">
                    <ul class="divide-y divide-gray-200">
                        <li v-for="session in sessions.data" :key="session.id" class="px-4 py-4 sm:px-6">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center">
                                        <p class="text-sm font-medium text-indigo-600 truncate">
                                            {{ session.task.name }}
                                        </p>
                                        <span v-if="session.session_category" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ session.session_category.name }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">
                                        {{ session.task.project.name }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ formatDate(session.started_at) }} • {{ session.duration_for_humans }}
                                    </p>
                                    <p v-if="session.comment" class="mt-2 text-sm text-gray-600">
                                        {{ session.comment }}
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <a :href="route('client-portal.sessions.show', session.id)" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        View Session
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Pagination -->
                <div v-if="sessions.links" class="mt-6">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a v-if="sessions.prev_page_url" :href="sessions.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a v-if="sessions.next_page_url" :href="sessions.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
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
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a v-if="sessions.prev_page_url" :href="sessions.prev_page_url" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        Previous
                                    </a>
                                    <a v-if="sessions.next_page_url" :href="sessions.next_page_url" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        Next
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </nav>
                </div>

                <!-- Empty state -->
                <div v-if="sessions.data.length === 0" class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No sessions</h3>
                    <p class="mt-1 text-sm text-gray-500">You don't have any sessions yet.</p>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
export default {
    props: {
        clientUser: Object,
        sessions: Object,
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
        }
    },
    mounted() {
        document.title = 'Sessions - Client Portal'
    }
}
</script>
