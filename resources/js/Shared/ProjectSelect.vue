<template>
    <div>
        <multi-select
            v-model="selectedProject"
            label="name"
            track-by="id"
            :options="projects"
        ></multi-select>
    </div>
</template>

<script>

    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'projects',
            'value',
        ],
        components: {
            multiSelect: multiSelect,
        },
        data() {
            return {
                selectedProject: this.projects.find(
                    project => project.id == this.value
                ),
            };
        },
        watch: {
            selectedProject(value) {
                this.$emit('input', value ? value.id : null);
            }
        }
    }

</script>