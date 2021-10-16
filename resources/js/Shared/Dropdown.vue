<template>
    <div class="relative float-right text-right" v-closeable="{
        exclude: ['button'],
        handler: 'onClose'
    }">
        <button class="btn float-right" @click="toggleIsOpen()">
            <span> {{ label || 'Actions' }} </span>
            <i class="fas fa-caret-down"></i>
        </button>
        <div class="rounded shadow-md mt-2 absolute mt-12 -ml-1 mb-12 top-0 left-0 min-w-full bg-white text-left" :class="isOpen || 'dropdown-closed'">
            <ul class="list-reset w-max-content min-w-full p-1">
                <li v-for="option in options" :key="option.name" class="clickable"> 
                    <a
                        v-on:click="optionWasClicked(option)"
                        class="px-4 py-2 block text-gray-700 font-normal hover:bg-grey-300 no-underline"
                    > {{ option.name }} </a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    props: [
        'options',
        'name',
        'selected-option',
    ],
    data() {
        return {
            isOpen: false,
            selected: this.selectedOption,
        }
    },
    methods: {
        optionWasClicked(option) {
            typeof option.event == 'string' ? events.$emit(option.event) : events.$emit(option.event.name, option.event.args);
            this.selected = option;
            this.isOpen = false;
        },
        toggleIsOpen() {
            this.isOpen = !this.isOpen;
        },
        onClose() {
            this.isOpen = false;
        }
    },
    computed: {
        label() {
            return this.selected ? this.selected.name : this.name;
        }
    },
}
</script>