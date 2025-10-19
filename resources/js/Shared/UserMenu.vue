<template>
    <Menu as="div" class="relative inline-block text-left">
        <MenuButton class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
            <div class="flex items-center gap-x-2">
                <div class="flex size-8 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ userInitials }}
                    </span>
                </div>
                <span class="hidden sm:block">{{ userName }}</span>
            </div>
            <ChevronDownIcon class="-mr-1 size-5 text-gray-400" aria-hidden="true" />
        </MenuButton>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg outline-1 outline-black/5 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
                <div class="py-1">
                    <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                        <div class="font-medium text-gray-900 dark:text-white">{{ userName }}</div>
                        <div class="text-xs">{{ userEmail }}</div>
                    </div>
                    <MenuItem v-slot="{ active }">
                        <a href="#" :class="[active ? 'bg-gray-100 text-gray-900 outline-hidden dark:bg-white/5 dark:text-white' : 'text-gray-700 dark:text-gray-300', 'block px-4 py-2 text-sm text-left']">
                            Your Profile
                        </a>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                        <a href="#" :class="[active ? 'bg-gray-100 text-gray-900 outline-hidden dark:bg-white/5 dark:text-white' : 'text-gray-700 dark:text-gray-300', 'block px-4 py-2 text-sm text-left']">
                            Settings
                        </a>
                    </MenuItem>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    <form method="POST" :action="logoutUrl">
                        <input type="hidden" name="_token" :value="page.props.csrf_token">
                        <MenuItem v-slot="{ active }">
                            <button type="submit" :class="[active ? 'bg-gray-100 text-gray-900 outline-hidden dark:bg-white/5 dark:text-white' : 'text-gray-700 dark:text-gray-300', 'block w-full px-4 py-2 text-left text-sm']">
                                Sign out
                            </button>
                        </MenuItem>
                    </form>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'

const props = defineProps({
    logoutUrl: {
        type: String,
        required: true
    }
})

const page = usePage()

const userName = computed(() => {
    return page.props.auth?.user?.name || page.props.auth?.clientUser?.name || 'User'
})

const userEmail = computed(() => {
    return page.props.auth?.user?.email || page.props.auth?.clientUser?.email || ''
})

const userInitials = computed(() => {
    const name = userName.value
    return name.split(' ').map(word => word.charAt(0)).join('').toUpperCase().slice(0, 2)
})
</script>
