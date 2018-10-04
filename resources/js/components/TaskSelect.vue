<template>
    <div>
        <multi-select v-model="task" label="name" :options="tasks"></multi-select>
    </div>
</template>

<script>

    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'tasks'
        ],
        components: {
            multiSelect: multiSelect,
        },
        data() {
            return {
                task: null,
            }
        }
    }

</script>