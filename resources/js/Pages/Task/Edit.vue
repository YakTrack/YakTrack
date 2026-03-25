<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Tasks',        url: route('task.index')},
                    {title: (isCreateForm ? 'Create' : 'Edit') + ' Task'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ isCreateForm ? 'Create' : 'Edit' }} Task</template>

        <form-layout
            :cancel-url="route('task.index')"
            :submit-text="isCreateForm ? 'Create' : 'Update'"
            :submit-loading-text="'Saving...'"
            :processing="processing"
            @submit="submit"
        >
            <form-field
                v-model="form.name"
                type="text"
                label="Task Name"
                placeholder="Enter task name"
                :required="true"
            />

            <form-field
                v-model="form.description"
                type="textarea"
                label="Description"
                placeholder="Describe this task"
                :rows="3"
            />

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Project
                </label>
                <multi-select
                    :options="projects"
                    label="name"
                    v-model="selectedProject"
                    placeholder="Select a project"
                />
            </div>

            <div v-if="selectedProject && selectedProject.task_statuses" class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Status
                </label>
                <multi-select
                    :options="selectedProject.task_statuses"
                    label="name"
                    v-model="selectedStatus"
                    placeholder="Select a status"
                >
                    <template slot="option" slot-scope="props">
                        <div class="flex items-center">
                            <div :style="{ backgroundColor: props.option.color }"
                                 class="w-3 h-3 rounded-full mr-2"></div>
                            {{ props.option.name }}
                        </div>
                    </template>
                    <template slot="singleLabel" slot-scope="props">
                        <div class="flex items-center">
                            <div :style="{ backgroundColor: props.option.color }"
                                 class="w-3 h-3 rounded-full mr-2"></div>
                            {{ props.option.name }}
                        </div>
                    </template>
                </multi-select>
            </div>
        </form-layout>
    </layout>
</template>

<script>
    import multiSelect from 'vue-multiselect';
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import layout from '@/Shared/Layout.vue';
    import formLayout from '@/Shared/FormLayout.vue';
    import formField from '@/Shared/FormField.vue';

    export default {
        props: {
            projects: {
                type: Array,
                required: true,
            },
            task: {
                type: Object,
                default: null,
            },
            tasks: {
                type: Array,
                default: () => [],
            },
        },
        data() {
            return {
                selectedProject: (this.task && this.task.project_id) ? this.projects.find(p => p.id == this.task.project_id) : null,
                selectedStatus: (this.task && this.task.status_id) ? this.findTaskStatus() : null,
                form: this.task || {},
            };
        },
        components: {
            multiSelect: multiSelect,
            breadcrumbs: breadcrumbs,
            layout: layout,
            formLayout: formLayout,
            formField: formField,
        },
        computed: {
            isCreateForm() {
                return this.task == null;
            },
            selectedProjectId() {
                return this.selectedProject ? this.selectedProject.id : null;
            },
            selectedStatusId() {
                return this.selectedStatus ? this.selectedStatus.id : null;
            },
            processing() {
                return this.$inertia.processing;
            },
        },
        watch: {
            selectedProject(newProject) {
                this.selectedStatus = null;

                if (!this.isCreateForm || !newProject || !newProject.task_code_prefix || this.form.name) {
                    return;
                }

                const projectTasks = this.tasks.filter(task => task.project_id === newProject.id);
                const pattern = new RegExp(`^${this.escapeRegExp(newProject.task_code_prefix)}-(\\d+):`);
                const numbers = projectTasks
                    .map(task => {
                        const match = task.name.match(pattern);
                        return match ? parseInt(match[1], 10) : null;
                    })
                    .filter(num => num !== null);
                const nextNumber = numbers.length > 0 ? Math.max(...numbers) + 1 : 1;
                this.form.name = `${newProject.task_code_prefix}-${String(nextNumber).padStart(4, '0')}: `;
            },
        },
        methods: {
            submit() {
                this.$inertia[this.isCreateForm ? 'post' : 'patch'](
                    this.isCreateForm ? route('task.store') : route('task.update', this.task.id),
                    {
                        ...this.form,
                        project_id: this.selectedProjectId,
                        status_id: this.selectedStatusId,
                    },
                );
            },
            findTaskStatus() {
                if (!this.task || !this.task.status_id) {
                    return null;
                }
                const project = this.projects.find(p => p.id == this.task.project_id);
                if (!project || !project.task_statuses) {
                    return null;
                }
                return project.task_statuses.find(s => s.id == this.task.status_id) || null;
            },
            escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            },
        },
    };
</script>
