<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs :breadcrumbs="breadcrumbItems"></breadcrumbs>
        </template>
        <template slot="title">
            {{ project ? project.name + ' - Task Statuses' : 'All Task Statuses' }}
        </template>
        <template slot="top-right-toolbar">
            <div class="flex space-x-2">
                <div v-if="!project" class="form-group">
                    <select v-model="selectedProjectId" @change="filterByProject" class="form-control">
                        <option value="">All Projects</option>
                        <option v-for="proj in projects" :key="proj.id" :value="proj.id">
                            {{ proj.name }}
                        </option>
                    </select>
                </div>
                <button-link :href="route('task-status.create', project ? {project_id: project.id} : {})" color="blue">
                    <i class="fa fa-plus text-blue-100 mr-2"></i>
                    Create Status
                </button-link>
            </div>
        </template>

        <div class="card" v-if="taskStatuses.length">
            <table class="table card-body">
                <thead>
                    <tr>
                        <th>Status Name</th>
                        <th v-if="!project">Project</th>
                        <th>Color</th>
                        <th>Order</th>
                        <th>Default</th>
                        <th>Completed</th>
                        <th>Task Count</th>
                        <th><span class="float-right">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="status in taskStatuses" :key="status.id" class="item-container">
                        <td>
                            <div class="flex items-center">
                                <div
                                    :style="{ backgroundColor: status.color }"
                                    class="w-3 h-3 rounded-full mr-2"
                                ></div>
                                {{ status.name }}
                            </div>
                        </td>
                        <td v-if="!project">
                            <Link
                                v-if="status.project"
                                :href="route('project.show', status.project.id)"
                            >
                                {{ status.project.name }}
                            </Link>
                        </td>
                        <td>
                            <span
                                :style="{ backgroundColor: status.color }"
                                class="px-2 py-1 text-xs rounded text-white inline-block"
                            >
                                {{ status.color }}
                            </span>
                        </td>
                        <td>{{ status.sort_order }}</td>
                        <td>
                            <i v-if="status.is_default" class="fa fa-check text-green-500"></i>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td>
                            <i v-if="status.is_completed" class="fa fa-check text-green-500"></i>
                            <span v-else class="text-gray-400">-</span>
                        </td>
                        <td>
                            {{ status.tasks_count || 0 }}
                        </td>
                        <td>
                            <div class="btn-group float-right">
                                <button-link :href="route('task-status.edit', status.id)">
                                    <i class="fa fa-edit text-gray-600 text-xs"></i>
                                </button-link>
                                <delete-button
                                    :url="route('task-status.destroy', status.id)"
                                    :disabled="status.tasks_count > 0"
                                    :title="status.tasks_count > 0 ? 'Cannot delete status with assigned tasks' : 'Delete status'"
                                >
                                    <i class="fa fa-trash text-gray-600 text-xs"></i>
                                </delete-button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="card-body" v-else>
            <p v-if="project">
                This project has no task statuses yet.
            </p>
            <p v-else>
                No task statuses have been created yet.
            </p>
            <button-link :href="route('task-status.create', project ? {project_id: project.id} : {})" color="blue" class="mt-2">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create First Status
            </button-link>
        </div>
    </layout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import breadcrumbs from '@/Shared/Breadcrumbs';
import deleteButton from '@/Shared/DeleteButton';
import layout from '@/Shared/Layout';

export default {
    props: [
        'taskStatuses',
        'project',
        'projects',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        deleteButton: deleteButton,
        layout: layout,
    },
    data() {
        return {
            selectedProjectId: this.project ? this.project.id : '',
        }
    },
    methods: {
        filterByProject() {
            if (this.selectedProjectId) {
                this.$inertia.get(route('task-status.index'), {
                    project_id: this.selectedProjectId
                });
            } else {
                this.$inertia.get(route('task-status.index'));
            }
        }
    },
    computed: {
        breadcrumbItems() {
            if (this.project) {
                // When viewing statuses for a specific project
                return [
                    {title: 'Home', url: route('home')},
                    {title: 'Projects', url: route('project.index')},
                    {title: this.project.name, url: route('project.show', this.project.id)},
                    {title: 'Task Statuses'}
                ];
            } else {
                // When viewing all statuses
                return [
                    {title: 'Home', url: route('home')},
                    {title: 'Task Statuses'}
                ];
            }
        }
    }
}
</script>