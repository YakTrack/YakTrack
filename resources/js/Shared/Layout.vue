<template>
    <div class="w-full">
        <header-nav v-if="!hideNavbar"></header-nav>
        
        <!-- Mobile header with hamburger menu -->
        <div v-if="!hideSidebar" class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-xs sm:px-6 lg:hidden dark:bg-gray-900 dark:shadow-none dark:after:pointer-events-none dark:after:absolute dark:after:inset-0 dark:after:border-b dark:after:border-white/10 dark:after:bg-black/10">
            <button type="button" class="-m-2.5 p-2.5 text-gray-700 hover:text-gray-900 lg:hidden dark:text-gray-400 dark:hover:text-white" @click="openSidebar">
                <span class="sr-only">Open sidebar</span>
                <Bars3Icon class="size-6" aria-hidden="true" />
            </button>
            <div class="flex-1 text-sm/6 font-semibold text-gray-900 dark:text-white">YakTrack</div>
        </div>

        <!-- Sidebar component -->
        <sidebar v-if="!hideSidebar" ref="sidebarRef" :open="sidebarOpen" @update:open="sidebarOpen = $event"></sidebar>

        <!-- Main content area -->
        <main class="pt-2 pb-10 sm:pt-16 lg:pl-72">
            <div class="px-3 pt-6 sm:px-6 lg:px-8">
                <div>
                    <slot name="breadcrumbs"></slot>
                    <messages></messages>
                    <div
                        class="flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3"
                        v-if="$slots.title || $slots['top-right-toolbar']"
                    >
                        <div class="flex-1" v-if="$slots.title">
                            <h1 class="text-3xl font-normal"><slot name="title"></slot></h1>
                        </div>
                        <div class="btn-toolbar mb-2 mb-md-0 flex-1 flex justify-end items-center" v-if="$slots['top-right-toolbar']">
                            <slot name="top-right-toolbar"></slot>
                        </div>
                    </div>
                </div>
                <slot></slot>
            </div>
        </main>
        
        <slot name="modals"></slot>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Bars3Icon } from '@heroicons/vue/24/outline'
import HeaderNav from '@/Shared/HeaderNav.vue'
import Messages from '@/Shared/Messages.vue'
import Sidebar from '@/Shared/Sidebar.vue'

const props = defineProps({
    hideNavbar: Boolean,
    hideSidebar: Boolean,
})

const sidebarOpen = ref(false)
const sidebarRef = ref(null)

// Function to open sidebar from mobile header
const openSidebar = () => {
    sidebarOpen.value = true
}

// Expose the sidebarOpen state for the sidebar component
defineExpose({
    sidebarOpen,
    openSidebar
})
</script>
