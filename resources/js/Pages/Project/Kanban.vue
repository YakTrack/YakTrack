<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs :breadcrumbs="[
                {title: 'Home', url: route('home')},
                {title: 'Projects', url: route('project.index')},
                {title: project.name, url: route('project.show', project.id)},
                {title: 'Board View'},
            ]"></breadcrumbs>
        </template>

        <!-- Project Header -->
        <div class="card">
            <h1 class="font-medium text-gray-dark">{{ project.name }}</h1>
            <div class="p4 mt-4">
                <i class="fa fa-user text-2xl text-gray-300 mr-2"></i>
                <span class="text-2xl font-light">{{ project.client.name }}</span>
            </div>
            <p class="mt-4">
                {{ project.description }}
            </p>

            <!-- View Switcher -->
            <div class="mt-4 flex gap-2">
                <a
                    :href="route('project.show', project.id)"
                    class="px-4 py-2 border rounded text-sm hover:bg-gray-50 transition"
                >
                    <i class="fa fa-list mr-1"></i>
                    List View
                </a>
                <a
                    :href="route('project.kanban', project.id)"
                    class="px-4 py-2 border rounded text-sm bg-blue-50 border-blue-500 text-blue-700"
                >
                    <i class="fa fa-columns mr-1"></i>
                    Board View
                </a>
            </div>
        </div>

        <!-- Kanban Board -->
        <div class="card mt-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-gray-dark">Task Board</h2>
                <button-link
                    :href="route('task.create', {project_id: project.id})"
                    color="blue"
                    size="sm"
                >
                    <i class="fa fa-plus mr-1"></i>
                    Add Task
                </button-link>
            </div>

            <!-- Empty state if no statuses -->
            <div v-if="!project.task_statuses || project.task_statuses.length === 0" class="text-center py-8 text-gray-500">
                <i class="fa fa-tasks text-4xl mb-4"></i>
                <p class="text-lg mb-2">No task statuses yet</p>
                <p class="mb-4">Create task statuses to organize your project's workflow.</p>
                <button-link
                    :href="route('task-status.create', {project_id: project.id})"
                    color="blue"
                >
                    <i class="fa fa-plus mr-2"></i>
                    Create First Status
                </button-link>
            </div>

            <!-- Kanban Columns -->
            <div v-else class="flex gap-4 overflow-x-auto pb-4">
                <div
                    v-for="status in project.task_statuses"
                    :key="status.id"
                    class="flex-shrink-0 w-80"
                >
                    <!-- Column Header -->
                    <div class="bg-gray-50 rounded-t-lg p-4 border border-b-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div
                                    :style="{ backgroundColor: status.color }"
                                    class="w-3 h-3 rounded-full mr-2"
                                ></div>
                                <h3 class="font-medium text-gray-dark">{{ status.name }}</h3>
                            </div>
                            <span class="text-sm text-gray-500">
                                {{ tasksByStatus(status.id).length }}
                            </span>
                        </div>
                    </div>

                    <!-- Column Body (Draggable Area) -->
                    <div class="border border-t-0 rounded-b-lg bg-gray-50 p-2 min-h-[200px]"
                         :style="{ maxHeight: 'calc(100vh - 400px)', overflowY: 'auto' }">
                        <draggable
                            :list="tasksByStatus(status.id)"
                            :group="{ name: 'tasks' }"
                            item-key="id"
                            class="space-y-2 min-h-[180px]"
                            @change="(evt) => handleTaskMove(evt, status.id)"
                            :disabled="isUpdating"
                        >
                            <template #item="{element}">
                                <task-card :task="element" />
                            </template>
                        </draggable>

                        <!-- Empty state for column -->
                        <div v-if="tasksByStatus(status.id).length === 0" class="text-center py-8 text-gray-400">
                            <i class="fa fa-inbox text-3xl mb-2"></i>
                            <p class="text-sm">No tasks</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import draggable from 'vuedraggable';
import taskCard from '@/components/TaskCard.vue';
import breadcrumbs from '@/Shared/Breadcrumbs.vue';
import layout from '@/Shared/Layout.vue';

export default {
    props: {
        project: Object,
        tasks: Array,
    },
    components: {
        draggable,
        taskCard,
        breadcrumbs,
        layout,
    },
    data() {
        return {
            localTasks: [...this.tasks],
            isUpdating: false,
        };
    },
    methods: {
        tasksByStatus(statusId) {
            return this.localTasks.filter(task => task.status_id === statusId);
        },
        async handleTaskMove(evt, newStatusId) {
            // Only process when a task is added to a new column
            if (!evt.added) {
                return;
            }

            const movedTask = evt.added.element;
            const oldStatusId = movedTask.status_id;

            // Optimistically update the task status
            const taskIndex = this.localTasks.findIndex(t => t.id === movedTask.id);
            if (taskIndex !== -1) {
                this.localTasks[taskIndex].status_id = newStatusId;
            }

            // Disable dragging during update
            this.isUpdating = true;

            try {
                // Send update to server using fetch for JSON API endpoint
                const response = await fetch(route('task.updateStatus', movedTask.id), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ status_id: newStatusId }),
                });

                const data = await response.json();

                if (!response.ok) {
                    // Revert on error
                    const taskIndex = this.localTasks.findIndex(t => t.id === movedTask.id);
                    if (taskIndex !== -1) {
                        this.localTasks[taskIndex].status_id = oldStatusId;
                    }

                    const errorMessage = data.errors?.status_id?.[0] || data.message || 'Failed to update task status';
                    alert(errorMessage);
                }
            } catch (error) {
                // Revert on network error
                const taskIndex = this.localTasks.findIndex(t => t.id === movedTask.id);
                if (taskIndex !== -1) {
                    this.localTasks[taskIndex].status_id = oldStatusId;
                }
                alert('Network error: Failed to update task status');
            } finally {
                this.isUpdating = false;
            }
        }
    }
}
</script>
