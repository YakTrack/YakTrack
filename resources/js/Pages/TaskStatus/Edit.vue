<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Projects', url: route('project.index')},
                    {title: project ? project.name : 'All Projects', url: project ? route('project.show', project.id) : route('project.index')},
                    {title: 'Task Statuses', url: route('task-status.index', project ? {project_id: project.id} : {})},
                    {title: isCreateForm ? 'Create' : 'Edit'}
                ]"
            ></breadcrumbs>
        </template>
        <template #title>
            {{ isCreateForm ? 'Create Task Status' : 'Edit Task Status' }}
        </template>

        <form @submit.prevent="submit" class="mt-2">
            <div class="form-group">
                <label for="name">Status Name</label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-control" 
                    placeholder="Enter status name (e.g., To Do, In Progress, Done)" 
                    v-model="form.name" 
                    required
                />
            </div>

            <div class="form-group">
                <label for="project_id">Project</label>
                <multi-select 
                    :options="projects" 
                    label="name" 
                    v-model="selectedProject"
                    placeholder="Select a project"
                    :disabled="!isCreateForm && taskStatus"
                ></multi-select>
                <small class="text-gray-500" v-if="!isCreateForm && taskStatus">
                    Project cannot be changed after creation
                </small>
            </div>

            <div class="form-group">
                <label for="color">Color</label>
                <div class="flex items-center space-x-2">
                    <input 
                        type="color" 
                        name="color" 
                        class="form-control w-16 h-10 border-gray-300 rounded"
                        v-model="form.color"
                    />
                    <input 
                        type="text" 
                        class="form-control flex-1"
                        placeholder="#6B7280"
                        v-model="form.color"
                        pattern="^#[0-9A-Fa-f]{6}$"
                    />
                    <div class="flex space-x-1">
                        <button 
                            type="button"
                            v-for="color in presetColors"
                            :key="color"
                            :style="{ backgroundColor: color }"
                            class="w-6 h-6 rounded border border-gray-300 cursor-pointer"
                            @click="form.color = color"
                            :title="color"
                        ></button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="sort_order">Sort Order</label>
                <input 
                    type="number" 
                    name="sort_order" 
                    class="form-control" 
                    placeholder="0"
                    v-model="form.sort_order"
                    min="0"
                />
                <small class="text-gray-500">Lower numbers appear first. Leave empty to add at the end.</small>
            </div>

            <div class="form-group">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_default" 
                            class="form-checkbox mr-2"
                            v-model="form.is_default"
                        />
                        Default Status
                    </label>
                    <small class="text-gray-500">New tasks will automatically get this status</small>
                </div>
            </div>

            <div class="form-group">
                <div class="flex items-center space-x-4">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="is_completed" 
                            class="form-checkbox mr-2"
                            v-model="form.is_completed"
                        />
                        Completed Status
                    </label>
                    <small class="text-gray-500">Tasks with this status are considered completed</small>
                </div>
            </div>

            <div class="form-group" v-if="form.color">
                <label>Preview</label>
                <div class="flex items-center space-x-2">
                    <span 
                        :style="{ backgroundColor: form.color }"
                        class="px-3 py-1 text-sm rounded-full text-white inline-block"
                    >
                        {{ form.name || 'Status Name' }}
                    </span>
                    <span class="text-gray-500">← This is how the status will appear</span>
                </div>
            </div>

            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <button-link :href="route('task-status.index', project ? {project_id: project.id} : {})">
                        Cancel
                    </button-link>
                </div>
                <div class="flex-1 float-right">
                    <button class="btn btn-blue float-right" :disabled="!selectedProject">
                        {{ isCreateForm ? 'Create Status' : 'Update Status' }}
                    </button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>
import multiSelect from 'vue-multiselect';
import breadcrumbs from '@/Shared/Breadcrumbs.vue';
import layout from '@/Shared/Layout.vue';

export default {
    props: [
        'projects',
        'taskStatus',
        'project',
    ],
    data() {
        return {
            selectedProject: (this.taskStatus && this.taskStatus.project_id) 
                ? this.projects.find(p => p.id == this.taskStatus.project_id)
                : (this.project || null),
            form: {
                name: this.taskStatus ? this.taskStatus.name : '',
                color: this.taskStatus ? this.taskStatus.color : '#6B7280',
                sort_order: this.taskStatus ? this.taskStatus.sort_order : '',
                is_default: this.taskStatus ? this.taskStatus.is_default : false,
                is_completed: this.taskStatus ? this.taskStatus.is_completed : false,
            },
            presetColors: [
                '#EF4444', // red
                '#F97316', // orange  
                '#EAB308', // yellow
                '#22C55E', // green
                '#3B82F6', // blue
                '#6366F1', // indigo
                '#8B5CF6', // purple
                '#EC4899', // pink
                '#6B7280', // gray
                '#1F2937', // dark gray
            ]
        }
    },
    components: {
        multiSelect: multiSelect,
        breadcrumbs: breadcrumbs,
        layout: layout,
    },
    methods: {
        submit() {
            const data = {
                ...this.form,
                project_id: this.selectedProjectId,
            };

            if (this.isCreateForm) {
                this.$inertia.post(route('task-status.store'), data);
            } else {
                this.$inertia.patch(route('task-status.update', this.taskStatus.id), data);
            }
        }
    },
    computed: {
        isCreateForm() {
            return this.taskStatus == null;
        },
        selectedProjectId() {
            return this.selectedProject ? this.selectedProject.id : null;
        }
    }
}
</script>

