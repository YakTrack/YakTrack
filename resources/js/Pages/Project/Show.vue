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

            <!-- View Switcher -->
            <div class="mt-4 flex gap-2">
                <a
                    :href="route('project.show', project.id)"
                    class="px-4 py-2 border rounded text-sm bg-blue-50 border-blue-500 text-blue-700"
                >
                    <i class="fa fa-list mr-1"></i>
                    List View
                </a>
                <a
                    :href="route('project.kanban', project.id)"
                    class="px-4 py-2 border rounded text-sm hover:bg-gray-50 transition"
                >
                    <i class="fa fa-columns mr-1"></i>
                    Board View
                </a>
            </div>

            <!-- Project tabs -->
            <div class="mt-6 flex flex-wrap gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
                <Link
                    :href="route('project.show', project.id) + '?tab=overview'"
                    class="px-4 py-2 rounded text-sm border transition"
                    :class="tab === 'overview'
                        ? 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-950/40 dark:border-blue-400 dark:text-blue-200'
                        : 'border-gray-200 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800'"
                >
                    <i class="fa fa-th-large mr-1"></i>
                    Overview
                </Link>
                <Link
                    :href="route('project.show', project.id) + '?tab=sessions'"
                    class="px-4 py-2 rounded text-sm border transition"
                    :class="tab === 'sessions'
                        ? 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-950/40 dark:border-blue-400 dark:text-blue-200'
                        : 'border-gray-200 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800'"
                >
                    <i class="fa fa-clock mr-1"></i>
                    Sessions
                </Link>
            </div>
        </div>

        <template v-if="tab === 'overview'">
        <!-- Jira -->
        <div class="card mt-4">
            <h2 class="text-lg font-medium text-gray-dark dark:text-gray-100 mb-2">Jira</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Connect a Jira Cloud site so you can create Yaktrack tasks from Jira issues.
                Create an API token in your
                <a
                    href="https://id.atlassian.com/manage-profile/security/api-tokens"
                    class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                    target="_blank"
                    rel="noopener noreferrer"
                >Atlassian account security settings</a>.
            </p>

            <div v-if="!jira.connected">
                <form @submit.prevent="submitJiraConnect">
                    <form-field
                        v-model="jiraForm.site_host"
                        type="text"
                        label="Jira site host"
                        placeholder="e.g. yourcompany.atlassian.net"
                        :required="true"
                        :error="errors.site_host"
                    />
                    <form-field
                        v-model="jiraForm.account_email"
                        type="email"
                        label="Atlassian account email"
                        placeholder="you@company.com"
                        :required="true"
                        :error="errors.account_email"
                    />
                    <form-field
                        v-model="jiraForm.api_token"
                        type="password"
                        label="API token"
                        placeholder="Your Jira API token"
                        :required="true"
                        :error="errors.api_token"
                    />
                    <button type="submit" class="btn btn-blue" :disabled="processing">
                        Connect Jira
                    </button>
                </form>
            </div>
            <div v-else>
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                    Connected to <strong class="font-medium">{{ jira.site_host }}</strong>
                </p>
                <button type="button" class="btn btn-default mb-6" @click="disconnectJira">
                    Disconnect Jira
                </button>

                <form @submit.prevent="submitJiraImport">
                    <form-field
                        v-model="importIssueKey"
                        type="text"
                        label="Import issue as task"
                        placeholder="e.g. PROJ-123"
                        :required="true"
                        :error="errors.issue_key"
                    />
                    <button type="submit" class="btn btn-blue" :disabled="processing">
                        Create task from Jira issue
                    </button>
                </form>
            </div>
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
        </template>

        <div v-else-if="tab === 'sessions' && sessions" class="mt-4">
            <project-sessions-tab
                :project="project"
                :sessions="sessions"
                :session-sprint-filters="sessionSprintFilters"
            />
        </div>
    </layout>
</template>

<script>

    import { Link } from '@inertiajs/vue3';
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import deleteButton from '@/Shared/DeleteButton.vue';
    import formField from '@/Shared/FormField.vue';
    import layout from '@/Shared/Layout.vue';
    import ProjectSessionsTab from '@/Pages/Project/ProjectSessionsTab.vue';

    export default {
        props: {
            project: {
                type: Object,
                required: true,
            },
            tab: {
                type: String,
                default: 'overview',
            },
            tasks: {
                type: Object,
                required: true,
            },
            sessions: {
                type: Object,
                default: null,
            },
            sessionsTable: {
                type: Object,
                default: null,
            },
            sessionSprintFilters: {
                type: Array,
                default: () => [],
            },
            jira: {
                type: Object,
                default: () => ({
                    connected: false,
                    site_host: null,
                }),
            },
        },
        data() {
            return {
                jiraForm: {
                    site_host: '',
                    account_email: '',
                    api_token: '',
                },
                importIssueKey: '',
            };
        },
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            formField: formField,
            layout: layout,
            ProjectSessionsTab,
        },
        computed: {
            errors() {
                return this.$page.props.errors || {};
            },
            processing() {
                return this.$inertia.processing;
            },
        },
        methods: {
            submitJiraConnect() {
                this.$inertia.post(route('project.jira.store', this.project.id), this.jiraForm);
            },
            disconnectJira() {
                if (confirm('Disconnect Jira from this project?')) {
                    this.$inertia.delete(route('project.jira.destroy', this.project.id));
                }
            },
            submitJiraImport() {
                this.$inertia.post(route('project.jira.import', this.project.id), {
                    issue_key: this.importIssueKey,
                });
            },
        },
    }

</script>
