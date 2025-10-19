<template>
    <client-portal-layout>
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
                                <a
                                    :href="route('client-portal.sessions.show', session.id)"
                                    class="text-base font-medium text-blue-800 truncate"
                                >
                                    {{ session.task.name }}
                                </a>
                            </div>
                            <p class="text-sm text-gray-500 truncate">
                                {{ session.task.project.name }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ formatDate(session.started_at) }} - {{ formatDate(session.ended_at) }}
                            </p>
                            <p v-if="session.comment" class="mt-2 text-sm text-gray-600">
                                {{ session.comment }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 pr-4">
                            <span v-if="session.session_category" class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ session.session_category.name }}
                            </span>
                        </div>
                        <div class="flex-shrink-0 font-mono text-gray-600 text-sm">
                            {{ session.durationForHumans }}
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
