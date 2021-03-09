<template>
    <div>
        <multi-select v-model="selectedTask" label="name" :options="tasks">
            <template slot="singleLabel" slot-scope="props">
                <span class="option__desc"><span class="option__title">{{ props.option.name }}</span></span>
                <span class="option__desc text-sm pl-2 font-light">{{ props.option.project ? props.option.project.name : '' }}</span>
                <span class="option__desc text-sm pl-2 font-thin">{{ props.option.project.client ? props.option.project.client.name : '' }}</span>
            </template>
            <template slot="option" slot-scope="props">
                <div class="option__desc">
                    <span class="option__title">{{ props.option.name }}</span>
                    <span class="option__title text-sm pl-2 font-light" v-if="props.option.project">{{ props.option.project ? props.option.project.name : '' }}</span>
                    <span class="option__title text-sm pl-2 font-thin" v-if="props.option.project">{{ props.option.project.client ? props.option.project.client.name : '' }}</span>
                </div>
            </template>
        </multi-select>
        <input type="hidden" name="task_id" :value="taskId">
    </div>
</template>

<script>

    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'tasks',
            'task',
            'onChange',
        ],
        components: {
            multiSelect: multiSelect,
        },
        data() {
            var _this = this;

            return {
                selectedTask: this.tasks.find(function (task) {
                    return task.id == _this.task;
                }),
            }
        },
        computed: {
            taskId() {
                if (this.selectedTask == null) {
                    return null;
                }

                return this.selectedTask.id;
            }
        },
        watch: {
            taskId(newValue) {
                if (this.onChange) {
                    this.onChange(newValue);
                }
            }
        }
    }

</script>