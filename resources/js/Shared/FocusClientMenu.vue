<template>
    <Menu as="div" class="relative inline-block text-left">
        <MenuButton class="flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs inset-ring-1 inset-ring-gray-300 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:shadow-none dark:inset-ring-white/5 dark:hover:bg-white/20">
            <span class="text-gray-500 dark:text-gray-400">Focus:</span>
            <span class="max-w-40 truncate">{{ focusedLabel }}</span>
            <ChevronDownIcon class="-mr-1 size-5 text-gray-400" aria-hidden="true" />
        </MenuButton>

        <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform scale-100" leave-active-class="transition ease-in duration-75" leave-from-class="transform scale-100" leave-to-class="transform opacity-0 scale-95">
            <MenuItems class="absolute right-0 z-10 mt-2 max-h-80 w-56 origin-top-right overflow-y-auto rounded-md bg-white shadow-lg outline-1 outline-black/5 dark:bg-gray-800 dark:shadow-none dark:-outline-offset-1 dark:outline-white/10">
                <div class="py-1">
                    <MenuItem v-slot="{ active }">
                        <button
                            type="button"
                            @click="setFocus(null)"
                            :class="[active ? 'bg-gray-100 text-gray-900 outline-hidden dark:bg-white/5 dark:text-white' : 'text-gray-700 dark:text-gray-300', !focusedClient ? 'font-semibold' : '', 'block w-full px-4 py-2 text-left text-sm']"
                        >
                            Clear focus / All clients
                        </button>
                    </MenuItem>
                    <div class="border-t border-gray-200 dark:border-gray-700"></div>
                    <MenuItem v-for="client in clients" :key="client.id" v-slot="{ active }">
                        <button
                            type="button"
                            @click="setFocus(client.id)"
                            :class="[active ? 'bg-gray-100 text-gray-900 outline-hidden dark:bg-white/5 dark:text-white' : 'text-gray-700 dark:text-gray-300', isFocused(client.id) ? 'font-semibold' : '', 'block w-full px-4 py-2 text-left text-sm']"
                        >
                            {{ client.name }}
                        </button>
                    </MenuItem>
                </div>
            </MenuItems>
        </transition>
    </Menu>
</template>

<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue'
import { ChevronDownIcon } from '@heroicons/vue/20/solid'

const page = usePage()

const clients = computed(() => page.props.focusableClients ?? [])
const focusedClient = computed(() => page.props.focusedClient ?? null)
const focusedLabel = computed(() => focusedClient.value ? focusedClient.value.name : 'All clients')

const isFocused = (id) => focusedClient.value !== null && focusedClient.value.id === id

const setFocus = (clientId) => {
    router.patch(route('focused-client.update'), { client_id: clientId }, {
        preserveScroll: true,
    })
}
</script>
