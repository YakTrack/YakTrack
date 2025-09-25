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
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Session Details</h1>
                            <p class="mt-2 text-gray-600">{{ session.task.name }} • {{ session.task.project.name }}</p>
                        </div>
                        <div class="flex space-x-3">
                            <a :href="route('client-portal.sessions.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Back to Sessions
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Session details -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <div class="px-4 py-5 sm:px-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Session Information</h3>
                        <p class="mt-1 max-w-2xl text-sm text-gray-500">Detailed information about this billable session</p>
                    </div>
                    <div class="border-t border-gray-200">
                        <dl>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Task</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <a :href="route('client-portal.tasks.show', session.task.id)" class="text-indigo-600 hover:text-indigo-500">
                                        {{ session.task.name }}
                                    </a>
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Project</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <a :href="route('client-portal.projects.show', session.task.project.id)" class="text-indigo-600 hover:text-indigo-500">
                                        {{ session.task.project.name }}
                                    </a>
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Started At</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ formatDateTime(session.started_at) }}
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Ended At</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ session.ended_at ? formatDateTime(session.ended_at) : 'Session still running' }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    {{ session.duration_for_humans }}
                                </dd>
                            </div>
                            <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Category</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <span v-if="session.session_category" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ session.session_category.name }}
                                    </span>
                                    <span v-else class="text-gray-500">No category</span>
                                </dd>
                            </div>
                            <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                                    <p v-if="session.notes" class="whitespace-pre-wrap">{{ session.notes }}</p>
                                    <p v-else class="text-gray-500">No notes provided</p>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<script>
export default {
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
