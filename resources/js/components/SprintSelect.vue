<template>
    <div>
        <multi-select v-model="selectedSprint" label="name" :custom-label="customLabel" :options="sprints">
            <template slot="option" slot-scope="slot">
                {{ slot.option.name }}
                <span class="text-grey-dark ml-2">{{ slot.option.project.name }}</span>
                <span class="text-grey ml-2">{{ slot.option.project.client.name }}</span>
            </template>
            <template slot="singleLabel" slot-scope="slot">
                <div class="pt-1">
                {{ slot.option.name }}
                <span class="text-grey-dark ml-2">{{ slot.option.project.name }}</span>
                <span class="text-grey ml-2">{{ slot.option.project.client.name }}</span>
                </div>
            </template>
        </multi-select>
        <input type="hidden" name="sprint_id" :value="sprintId">
    </div>
</template>

<script>

    import multiSelect from 'vue-multiselect';

    export default {
        props: {
            onChange: {
                type: Function,
                default: () => null,
            },
            sprints: {
                type: Array,
                default: () => [],
            },
            sprint: {
                default: null,
            },
        },
        components: {
            multiSelect: multiSelect,
        },
        data() {
            return {
                selectedSprint: this.sprints.find(sprint => sprint.id == this.sprint),
            }
        },
        computed: {
            sprintId() {
                if (this.selectedSprint == null) {
                    return null;
                }

                return this.selectedSprint.id;
            }
        },
        watch: {
            sprintId(newValue) {
                this.onChange(newValue);
            }
        },
        methods: {
            customLabel(option) {
                return option.name + ' ' + option.project.name + ' ' + option.project.client.name;
            }
        }
    }

</script>