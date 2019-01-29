<template>
    <div>
        <form :action="url" method="post">

            <div class="form-group">
                <label for="name"> Task Name </label>
                <input type="text" name="name" class="form-control" placeholder="Enter task name"/>
            </div>

            <div class="form-group">
                <label for="description"> Description </label>
                <textarea name="description" class="form-control" placeholder="Describe this task"></textarea>
            </div>

            <div class="form-group">
                <label for="project_id"> Project </label>
                <input type="hidden" name="project_id" :value="selectedProjectId"/>
                <multi-select :options="projects" label="name" v-model="selectedProject"></multi-select>
            </div>

            <div class="form-group">
                <label for="parent_id"> Parent Task </label>
                <input type="hidden" name="parent_id" :value="selectedParentTaskId"/>
                <multi-select :options="selectableParentTasks" label="name" v-model="selectedParentTask"></multi-select>
            </div>
            <csrf-input></csrf-input>
            <div class="form-group float-right">
                <button class="btn btn-blue"> Create </button>
            </div>
        </form>

    </div>
</template>

<script>
    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'url',
            'projects',
            'sprints',
            'tasks',
        ],
        data() {
            return {
                selectedProject: null,
                selectedSprint: null,
                selectedParentTask: null,
            }
        },
        components: {
            multiSelect: multiSelect,
        },
        computed: {
            selectedProjectId() {
                return this.selectedProject ? this.selectedProject.id : null;
            },
            selectedSprintId() {
                return this.selectedSprint ? this.selectedSprint.id : null;
            },
            selectedParentTaskId() {
                return this.selectedParentTask ? this.selectedParentTask.id : null;
            },
            selectableSprints() {
                var _this = this;
                return this.selectedProject ? this.sprints.filter(function (sprint) {
                    return sprint.project_id === _this.selectedProjectId;
                }) : this.sprints;
            },
            selectableParentTasks() {
                var _this = this;

                return this.selectedProject ? 
                    this.selectedSprint ? this.tasks.filter(function (task) {
                        return task.sprint_id === _this.selectedSprintId;
                    }) : this.tasks.filter(function (task) {
                        return task.project_id === _this.selectedProjectId;
                    }) : this.tasks;
            }
        }
    }
</script>
