<template>
    <Menu as="div" class="relative inline-block">
        <MenuButton class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-150">
            <i class="fas fa-ellipsis-v"></i>
        </MenuButton>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems :class="[dropdownClasses, 'absolute z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg outline-1 outline-black/5 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10']">
                <div class="py-1">
                    <MenuItem v-for="option in options" :key="option.name" v-slot="{ active }">
                        <a
                            href="#"
                            @click.prevent="optionWasClicked(option)"
                            :class="[active ? 'bg-gray-100 text-gray-900 outline-hidden dark:bg-white/5 dark:text-white' : 'text-gray-700 dark:text-gray-300', 'block px-4 py-2 text-sm text-left']"
                        >
                            {{ option.name }}
                        </a>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script setup>
import { computed } from 'vue'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'

const props = defineProps({
    options: {
        type: Array,
        required: true
    },
    direction: {
        type: String,
        default: 'right'
    }
})

const optionWasClicked = (option) => {
    if (option.callback) {
        option.callback();
    } else if (option.event) {
        typeof option.event == 'string' ? events.emit(option.event) : events.emit(option.event.name, option.event.args);
    }
}

const dropdownClasses = computed(() => {
    if (props.direction === 'left') {
        return 'right-0';
    }
    return 'left-0';
})
</script>
