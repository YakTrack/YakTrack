<template>
    <div class="relative text-right" v-closeable="{
        exclude: ['button'],
        handler: 'onClose'
    }">
        <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-150" @click="toggleIsOpen()">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        <div class="rounded shadow-md absolute top-full right-0 mt-1 min-w-full bg-white text-left z-10" :class="[dropdownClasses, isOpen || 'dropdown-closed']">
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
        'direction',
    ],
    data() {
        return {
            isOpen: false,
        }
    },
    methods: {
        optionWasClicked(option) {
            typeof option.event == 'string' ? events.emit(option.event) : events.emit(option.event.name, option.event.args);
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
        dropdownClasses() {
            if (this.direction === 'left') {
                return 'right-0';
            }
            return 'left-0';
        }
    },
}
</script>
