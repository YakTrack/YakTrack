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
        <template #title> Edit Task </template>
        <form @submit.prevent="submit" class="mt-2">
            <div class="form-group">
                <label for="name"> Task Name </label>
                <input type="text" name="name" class="form-control" placeholder="Enter task name" v-model="form.name" required/>
            </div>

            <div class="form-group">
                <label for="description"> Description </label>
                <textarea name="description" class="form-control" placeholder="Describe this task" v-model="form.description"></textarea>
            </div>

            <div class="form-group">
                <label for="project_id"> Project </label>
                <multi-select :options="projects" label="name" v-model="selectedProject"></multi-select>
            </div>

            <div class="form-group">
                <label for="parent_id"> Parent Task </label>
                <multi-select :options="selectableParentTasks" label="name" v-model="selectedParentTask"></multi-select>
            </div>

            <div class="form-group" v-if="selectedProject && selectedProject.task_statuses">
                <label for="status_id"> Status </label>
                <multi-select :options="selectedProject.task_statuses" label="name" v-model="selectedStatus">
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
            <div class="flex mt-4">
                <div class="flex-1 mt-2">
                    <button-link :href="route('task.index')"> Cancel </button-link>
                </div>
                <div class="flex-1 float-right">
                    <button class="btn btn-blue float-right"> {{ isCreateForm ? 'Create' : 'Update' }} </button>
                </div>
            </div>
        </form>
    </layout>
</template>

<script>
    import multiSelect from 'vue-multiselect';
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';
    import projectSelect from '@/Shared/ProjectSelect';

    export default {
        props: [
            'projects',
            'task',
            'tasks',
        ],
        data() {
            return {
                selectedProject: (this.task && this.task.project_id) ? this.projects.find(p => p.id == this.task.project_id) : null,
                selectedParentTask: (this.task && this.task.parent_id) ? this.tasks.find(t => t.id == this.task.parent_id) : null,
                selectedStatus: (this.task && this.task.status_id) ? this.findTaskStatus() : null,
                form: this.task || {},
            }
        },
        components: {
            multiSelect: multiSelect,
            breadcrumbs: breadcrumbs,
            layout: layout,
            projectSelect: projectSelect,
        },
        methods: {
            submit() {
                this.$inertia[this.isCreateForm ? 'post' : 'patch'](
                    this.isCreateForm ? route('task.store') : route('task.update', this.task.id),
                    {
                        ...this.form,
                        ...{
                            project_id: this.selectedProjectId,
                            parent_id: this.selectedParentTaskId,
                            status_id: this.selectedStatusId,
                        }
                    }
                );
            },
            findTaskStatus() {
                if (!this.task || !this.task.status_id) return null;
                const project = this.projects.find(p => p.id == this.task.project_id);
                if (!project || !project.task_statuses) return null;
                return project.task_statuses.find(s => s.id == this.task.status_id) || null;
            }
        },
        computed: {
            isCreateForm() {
                return this.task == null;
            },
            selectedProjectId() {
                return this.selectedProject ? this.selectedProject.id : null;
            },
            selectedParentTaskId() {
                return this.selectedParentTask ? this.selectedParentTask.id : null;
            },
            selectedStatusId() {
                return this.selectedStatus ? this.selectedStatus.id : null;
            },
            selectableParentTasks() {
                var _this = this;

                return this.selectedProject ? this.tasks.filter(function (task) {
                    return task.project_id === _this.selectedProjectId;
                }) : this.tasks;
            }
        },
        watch: {
            selectedProject(newProject) {
                // Reset status when project changes
                this.selectedStatus = null;
            }
        }
    }
</script>