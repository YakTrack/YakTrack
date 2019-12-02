<template>
    <div>
        <multi-select v-model="selectedClient" @input="handleInput" label="name" :options="clients"></multi-select>
        <input type="hidden" name="client_id" :value="clientId">
    </div>
</template>

<script>

    import multiSelect from 'vue-multiselect';

    export default {
        props: [
            'clients',
            'client',
            'value',
        ],
        components: {
            multiSelect: multiSelect,
        },
        data() {
            var _this = this;

            return {
                selectedClient: this.clients.find(function (client) {
                    return client.id == _this.client || _this.value;
                }),
            }
        },
        methods: {
            handleInput (e) {
                this.$emit('input', this.clientId)
            }
        },
        computed: {
            clientId() {
                if (this.selectedClient == null) {
                    return null;
                }

                return this.selectedClient.id;
            }
        },
    }

</script>