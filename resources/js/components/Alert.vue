<template>
    <div v-if="isVisible" class="rounded shadow mb-8 mt-8 p-8 text-lg" :class="alertClass">
        {{ alert.message }}
        <button type="button" class="float-right" @click="closeAlert" aria-label="Close">
            <span aria-hidden="true" class="text-2xl text-gray-800">&times;</span>
        </button>
    </div>
</template>

<script>
    export default {
        props: ['alert'],
        data() {
            return {
                isVisible: true,
            }
        },
        methods: {
            closeAlert() {
                this.isVisible = false;
            },
        },
        computed: {
            alertClass() {
                return this.alert.type == 'success' ? 'bg-green-100 text-green-900' : 'bg-red-100 text-red-900';
            }
        },
        mounted() {
            setTimeout(this.closeAlert, 10000);
        }
    }
</script>