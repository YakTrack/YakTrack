<template>
    <div>
        <multi-select
            v-model="selectedClient"
            label="name"
            track-by="id"
            :options="clients"
        ></multi-select>
    </div>
</template>

<script>

    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'clients',
            'value',
        ],
        components: {
            multiSelect: multiSelect,
        },
        data() {
            return {
                selectedClient: this.clients.find(
                    client => client.id == this.value
                ),
            };
        },
        watch: {
            selectedClient(value) {
                this.$emit('input', value ? value.id : null);
            }
        }
    }

</script>