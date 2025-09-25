<template>
    <layout>
        <template slot="breadcrumbs">
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
    </layout>
</template>

<script>

    import breadcrumbs from '@/Shared/Breadcrumbs';
    import deleteButton from '@/Shared/DeleteButton';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'project',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
        }
    }

</script>
