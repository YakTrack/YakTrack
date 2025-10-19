<template>
    <Menu as="div" class="relative inline-block">
        <MenuButton class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
            {{ label || 'Actions' }}
            <ChevronDownIcon class="-mr-1 size-5 text-gray-400" aria-hidden="true" />
        </MenuButton>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems :class="[dropdownClasses, 'absolute z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg outline-1 outline-black/5 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10']">
                <div class="py-1">
                    <MenuItem v-for="option in options" :key="option.name" v-slot="{ active }">
                        <a
                            href="#"
                            @click.prevent="optionWasClicked(option)"
                            :class="[active ? 'text-left bg-gray-100 text-gray-900 outline-hidden dark:bg-white/5 dark:text-white' : 'text-gray-700 dark:text-gray-300', 'block px-4 py-2 text-sm text-left']"
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
import { ChevronDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    options: {
        type: Array,
        required: true
    },
    name: {
        type: String,
        default: null
    },
    selectedOption: {
        type: Object,
        default: null
    },
    direction: {
        type: String,
        default: 'right'
    }
})

const optionWasClicked = (option) => {
    typeof option.event == 'string' ? events.emit(option.event) : events.emit(option.event.name, option.event.args);
}

const label = computed(() => {
    return props.name;
})

const dropdownClasses = computed(() => {
    if (props.direction === 'left') {
        return 'right-0';
    }
    return 'left-0 -ml-1';
})
</script>
