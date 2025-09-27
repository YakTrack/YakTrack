<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs :breadcrumbs="[
                {title: 'Home',         url: route('home')},
                {title: 'Projects',     url: route('project.index')},
                {title: project.name},
            ]" ></breadcrumbs>
        </template>
        <div class="card">
            <h1 class="font-medium text-gray-dark"> {{ project.name }} </h1>
            <div class="p4 mt-4">
                <i class="fa fa-user text-2xl text-gray-300 mr-2"></i>
                <span class="text-2xl font-light"> {{ project.client.name }} </span>
            </div>
            <p class="mt-4">
                {{ project.description }}
            </p>
        </div>

        <!-- Task Statuses Section -->
        <div class="card mt-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-dark">Task Statuses</h2>
                <div class="flex space-x-2">
                    <button-link 
                        :href="route('task-status.create', {project_id: project.id})" 
                        color="blue"
                        size="sm"
                    >
                        <i class="fa fa-plus mr-1"></i>
                        Add Status
                    </button-link>
                    <button-link
                        :href="route('task-status.index', {project_id: project.id})"
                        size="sm"
                    >
                        <i class="fa fa-list mr-1"></i>
                        View All
                    </button-link>
                </div>
            </div>

            <div v-if="project.task_statuses && project.task_statuses.length > 0">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div
                        v-for="status in project.task_statuses"
                        :key="status.id"
                        class="border rounded-lg p-4 hover:shadow-md transition-shadow"
                    >
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div
                                    :style="{ backgroundColor: status.color }"
                                    class="w-4 h-4 rounded-full mr-3"
                                ></div>
                                <h3 class="font-medium text-gray-dark">{{ status.name }}</h3>
                            </div>
                            <div class="flex space-x-1">
                                <span
                                    v-if="status.is_default"
                                    class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full"
                                    title="Default status for new tasks"
                                >
                                    Default
                                </span>
                                <span
                                    v-if="status.is_completed"
                                    class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full"
                                    title="Completed status"
                                >
                                    Completed
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between text-sm text-gray-600">
                            <span>
                                {{ status.tasks_count }} task{{ status.tasks_count !== 1 ? 's' : '' }}
                            </span>
                            <div class="flex space-x-2">
                                <button-link
                                    :href="route('task-status.edit', status.id)"
                                    size="xs"
                                    class="text-gray-500 hover:text-gray-700"
                                >
                                    <i class="fa fa-edit"></i>
                                </button-link>
                                <delete-button
                                    :url="route('task-status.destroy', status.id)"
                                    :disabled="status.tasks_count > 0"
                                    :title="status.tasks_count > 0 ? 'Cannot delete status with assigned tasks' : 'Delete status'"
                                    size="xs"
                                    class="text-gray-500 hover:text-red-600"
                                >
                                    <i class="fa fa-trash"></i>
                                </delete-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
                <i class="fa fa-tasks text-4xl mb-4"></i>
                <p class="text-lg mb-2">No task statuses yet</p>
                <p class="mb-4">Create task statuses to organize and track your project's workflow.</p>
                <button-link
                    :href="route('task-status.create', {project_id: project.id})" 
                    color="blue"
                >
                    <i class="fa fa-plus mr-2"></i>
                    Create First Status
                </button-link>
            </div>
        </div>

        <!-- Tasks Section -->
        <div class="card mt-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-dark">Tasks</h2>
                <button-link
                    :href="route('task.create', {project_id: project.id})"
                    color="blue"
                    size="sm"
                >
                    <i class="fa fa-plus mr-1"></i>
                    Add Task
                </button-link>
            </div>

            <div v-if="tasks.data && tasks.data.length > 0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="pl-0 pr-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Parent
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="pl-6 pr-0 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="task in tasks.data" :key="task.id" class="hover:bg-gray-50">
                                <td class="pr-6 py-1 whitespace-nowrap">
                                    <a :href="route('task.show', task.id)" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        {{ task.name }}
                                    </a>
                                </td>
                                <td class="px-6 py-1 whitespace-nowrap text-sm text-gray-900">
                                    <a v-if="task.parent" :href="route('task.show', task.parent.id)" class="text-blue-600 hover:text-blue-900">
                                        {{ task.parent.name }}
                                    </a>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="px-6 py-1 whitespace-nowrap">
                                    <span v-if="task.task_status" 
                                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                          :style="{ backgroundColor: task.task_status.color + '20', color: task.task_status.color }">
                                        {{ task.task_status.name }}
                                    </span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="pl-6 py-1 whitespace-nowrap text-right text-sm font-medium flex items-center justify-end">
                                    <button-link
                                        :href="route('task.edit', task.id)"
                                        size="xs"
                                        class="text-gray-500 hover:text-gray-700 mr-2"
                                    >
                                        <i class="fa fa-edit"></i>
                                    </button-link>
                                    <delete-button
                                        :url="route('task.destroy', task.id)"
                                        size="xs"
                                        class="text-gray-500 hover:text-red-600"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </delete-button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Tasks Pagination -->
                <div v-if="tasks.links" class="mt-6">
                    <nav class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a v-if="tasks.prev_page_url" :href="tasks.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a v-if="tasks.next_page_url" :href="tasks.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing
                                    <span class="font-medium">{{ tasks.from }}</span>
                                    to
                                    <span class="font-medium">{{ tasks.to }}</span>
                                    of
                                    <span class="font-medium">{{ tasks.total }}</span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <a v-if="tasks.prev_page_url" :href="tasks.prev_page_url" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        Previous
                                    </a>
                                    <a v-if="tasks.next_page_url" :href="tasks.next_page_url" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                        Next
                                    </a>
                                </nav>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>

            <div v-else class="text-center py-8 text-gray-500">
                <i class="fa fa-tasks text-4xl mb-4"></i>
                <p class="text-lg mb-2">No tasks yet</p>
                <p class="mb-4">Tasks will appear here as they are created for this project.</p>
                <button-link
                    :href="route('task.create', {project_id: project.id})"
                    color="blue"
                >
                    <i class="fa fa-plus mr-2"></i>
                    Create First Task
                </button-link>
            </div>
        </div>

        <!-- Sessions Section -->
        <div class="card mt-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-dark">Recent Sessions</h2>
            </div>

            <div v-if="sessions.data && sessions.data.length > 0">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="pl-0 pr-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Task
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Billable
                                </th>
                                <th class="pl-6 pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Comment
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="session in sessions.data" :key="session.id" class="hover:bg-gray-50">
                                <td class="pr-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(session.ended_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a v-if="session.task" :href="route('task.show', session.task.id)" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        {{ session.task.name }}
                                    </a>
                                    <span v-else class="text-sm text-gray-500"> - </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ session.session_category ? session.session_category.name : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDuration(session.started_at, session.ended_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span v-if="session.is_billable" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Yes
                                    </span>
                                    <span v-else class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        No
                                    </span>
                                </td>
                                <td class="pl-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                    {{ session.comment || '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
            </div>

            <div v-else class="text-center py-8 text-gray-500">
                <i class="fa fa-clock text-4xl mb-4"></i>
                <p class="text-lg mb-2">No sessions yet</p>
                <p class="mb-4">Sessions will appear here as work is tracked on this project.</p>
            </div>
        </div>
    </layout>
</template>

<script>

    import breadcrumbs from '@/Shared/Breadcrumbs';
    import deleteButton from '@/Shared/DeleteButton';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'project',
            'sessions',
            'tasks',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
        },
        methods: {
            formatDate(date) {
                return new Date(date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            },
            formatDuration(startedAt, endedAt) {
                const start = new Date(startedAt);
                const end = new Date(endedAt);
                const diffMs = end - start;
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                if (diffHours > 0) {
                    return `${diffHours}h ${diffMinutes}m`;
                }

                return `${diffMinutes}m`;
            }
        }
    }

</script>
