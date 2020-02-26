<template>
    <div v-if="isVisible" class="rounded mb-8 mt-8 p-8 text-white text-lg" :class="alertClass">
        {{ alert.message }}
        <button type="button" class="float-right" @click="closeAlert" aria-label="Close">
            <span aria-hidden="true" class="text-lg text-white">&times;</span>
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
                return this.alert.type == 'success' ? 'bg-green' : 'bg-red';
            }
        }
    }
</script>