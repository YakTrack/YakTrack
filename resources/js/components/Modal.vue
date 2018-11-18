<template>
    <div v-if="modalIsOpen" @click.self="toggleModal" class="animated fadeIn fixed z-50 pin overflow-auto bg-smoke-dark flex">
        <div class="animated fadeInUp fixed shadow-inner max-w-md md:relative pin-b pin-x align-top m-auto justify-end md:justify-center p-8 bg-white md:rounded w-full md:h-auto md:shadow flex flex-col">
            <slot :payload="payload"></slot>
            <div class="flex">
                <button @click="toggleModal" class="btn mr-auto">
                    {{ cancelButtonText }}
                </button>
                <button @click="onSubmit" class="btn btn-green">
                    {{ primaryButtonText }}
                </button>
            </div>
            <span @click="toggleModal" class="absolute pin-t pin-r pt-4 px-4">
                <svg class="h-12 w-12 text-grey hover:text-grey-darkest" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            openOn: {
                type: String,
            },
            closeOn: {
                type: String,
            },
            primaryButtonText: {
                type: String,
                default: "Proceed",
            },
            cancelButtonText: {
                type: String,
                default: "Cancel",
            },
            onSubmit: {
                type: Function,
                default: () => null,
            },
        },
        data: function () {
            return {
                modalIsOpen: false,
                payload: null,
            }
        },
        created() {
            events.$on(this.openOn, () => this.modalIsOpen = true);
            events.$on(this.closeOn, () => this.modalIsOpen = false);
        },
        methods: {
            toggleModal() {
                this.modalIsOpen = !this.modalIsOpen
            },
            submit() {
                this.onSubmit(this.payload);
            }
        }
    }

</script>