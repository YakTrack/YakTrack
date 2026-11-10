<template>
    <client-portal-layout>
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Session Details</h1>
                            <p class="mt-2 text-gray-600 dark:text-gray-300">{{ session.task.name }} • {{ session.task.project.name }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a :href="route('client-portal.sessions.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:border-gray-700 dark:text-gray-300 dark:bg-gray-800 dark:hover:bg-gray-700/50 dark:focus:ring-offset-gray-900">
                                Back to Sessions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Session details -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg dark:bg-gray-800">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100">Session Information</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">Detailed information about this billable session</p>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700">
                        <dl>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-gray-800/60">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Task</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
                                    <a :href="route('client-portal.tasks.show', session.task.id)" class="text-indigo-600 hover:text-indigo-500">
                                        {{ session.task.name }}
                                    </a>
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-gray-800">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Project</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
                                    <a :href="route('client-portal.projects.show', session.task.project.id)" class="text-indigo-600 hover:text-indigo-500">
                                        {{ session.task.project.name }}
                                    </a>
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-gray-800/60">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Started At</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
                                    {{ formatDateTime(session.started_at) }}
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-gray-800">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Ended At</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
                                    {{ session.ended_at ? formatDateTime(session.ended_at) : 'Session still running' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-gray-800/60">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
                                    {{ session.duration_for_humans }}
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-gray-800">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
                                    <span v-if="session.session_category" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        {{ session.session_category.name }}
                                    </span>
                                    <span v-else class="text-gray-500 dark:text-gray-400">No category</span>
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 dark:bg-gray-800/60">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2 dark:text-gray-100">
                                    <p v-if="session.comment" class="whitespace-pre-wrap">{{ session.comment }}</p>
                                    <p v-else class="text-gray-500 dark:text-gray-400">No notes provided</p>
                                </dd>
                            </div>
                        </dl>
                    </div>
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
        session: Object,
    },
    methods: {
        formatDateTime(dateString) {
            return new Date(dateString).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            })
        }
    },
    mounted() {
        document.title = `Session ${this.session.id} - Client Portal`
    }
}
</script>
