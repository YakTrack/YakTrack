<template>
    <div>
        <multi-select
            v-model="selectedSessionCategory"
            label="name"
            track-by="id"
            :options="sessionCategories"
        ></multi-select>
    </div>
</template>

<script>

    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'sessionCategories',
            'value',
        ],
        components: {
            multiSelect: multiSelect,
        },
        data() {
            return {
                selectedSessionCategory: this.sessionCategories.find(
                    sessionCategory => sessionCategory.id == this.value
                ),
            };
        },
        watch: {
            selectedSessionCategory(value) {
                this.$emit('input', value ? value.id : null);
            }
        }
    }

</script>