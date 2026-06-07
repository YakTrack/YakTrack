<template>
    <div :class="{ 'multiselect-compact': compact }">
        <multi-select
            v-model="selectedSprint"
            label="name"
            :custom-label="customLabel"
            :options="sprints"
            :use-teleport="useTeleport"
        >
            <template slot="option" slot-scope="slot" v-if="slot.option">
                {{ slot.option.name }}
                <span class="text-gray-600 ml-2" v-if="projectNames(slot.option)">{{ projectNames(slot.option) }}</span>
            </template>
            <template slot="singleLabel" slot-scope="slot" v-if="slot.option">
                <span v-if="compact" class="block truncate" :title="customLabel(slot.option)">
                    {{ slot.option.name }}
                </span>
                <template v-else>
                    {{ slot.option.name }}
                    <span class="text-gray-600 ml-2" v-if="projectNames(slot.option)">{{ projectNames(slot.option) }}</span>
                </template>
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
            compact: {
                type: Boolean,
                default: false,
            },
            useTeleport: {
                type: Boolean,
                default: false,
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
            },
            sprint(newValue) {
                this.selectedSprint = this.sprints.find(sprint => sprint.id == newValue) ?? null;
            },
        },
        methods: {
            projectNames(option) {
                if (Array.isArray(option.projects) && option.projects.length > 0) {
                    return option.projects.map(project => project.name).join(', ');
                }

                if (option.project) {
                    return option.project.name;
                }

                return '';
            },
            customLabel(option) {
                return [
                    option.name,
                    this.projectNames(option),
                ].filter(s => s.length > 0).join(' ');
            }
        }
    }

</script>
