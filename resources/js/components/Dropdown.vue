<template>
    <div class="relative float-right text-right">
        <button class="btn float-right" @click="toggleIsOpen()">
            <span>Actions</span>
            <i class="fas fa-caret-down"></i>
        </button>
        <div class="rounded shadow-md mt-2 absolute mt-12 pin-t pin-l min-w-full bg-white" :class="isOpen || 'dropdown-closed'">
            <ul class="list-reset">
                <li v-for="option in options" :key="option.name" >
                    <a v-on:click="optionWasClicked(option)" href="#" class="px-4 py-2 block text-black hover:bg-grey-light no-underline"> {{ option.name }} </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'options'
    ],
    data() {
        return {
            isOpen: false,
        }
    },
    methods: {
        optionWasClicked(option) {
            events.$emit(option.event);
        },
        toggleIsOpen() {
            this.isOpen = !this.isOpen;
        }
    }
}
</script>

<style>
    .dropdown-closed {
        display: none;
    }
</style>