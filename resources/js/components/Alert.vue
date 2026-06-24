<template>
    <div
        class="pointer-events-auto w-full max-w-sm rounded-lg border px-4 py-3 shadow-lg"
        :class="alertClass"
        role="alert"
    >
        <div class="flex items-start justify-between gap-3">
            <p class="text-sm font-medium leading-5">{{ alert.message }}</p>
            <button
                type="button"
                class="shrink-0 text-lg leading-none opacity-60 transition-opacity hover:opacity-100"
                aria-label="Close"
                @click="close"
            >
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['alert'],
        emits: ['dismiss'],
        mounted() {
            this.timeout = setTimeout(() => {
                this.close();
            }, 10000);
        },
        beforeUnmount() {
            clearTimeout(this.timeout);
        },
        methods: {
            close() {
                clearTimeout(this.timeout);
                this.$emit('dismiss');
            },
        },
        computed: {
            alertClass() {
                return this.alert.type === 'success'
                    ? 'bg-green-50 border-green-200 text-green-900'
                    : 'bg-red-50 border-red-200 text-red-900';
            },
        },
    }
</script>
