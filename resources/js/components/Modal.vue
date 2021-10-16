<template>
    <div v-if="modalIsOpen" @click.self="toggleModal" class="modal opacity-100 inset-x-0 bottom-0 fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">
        <div class="modal-overlay fixed w-full h-full bg-gray-900 opacity-50 z-50"></div>
        <div class="modal-container bg-white p-4 w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto opacity-100 relative">
            <slot :payload="payload"></slot>
            <div class="flex">
                <button @click="toggleModal" class="btn mr-auto">
                    {{ cancelButtonText }}
                </button>
                <button @click="onSubmit" class="btn btn-green">
                    {{ primaryButtonText }}
                </button>
            </div>
            <span @click="toggleModal" class="absolute top-0 right-0 pt-4 px-4">
                <svg class="h-12 w-12 text-gray-500 hover:text-gray-900" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
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
            events.$on(this.openOn, () => {
                this.modalIsOpen = true
            });
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