<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation -->
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a :href="route('client-portal.dashboard')" class="text-2xl font-bold text-gray-900">
                                <b>Y</b>ak<b>T</b>rack <span class="text-sm text-gray-600">Client Portal</span>
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a :href="route('client-portal.dashboard')" :class="navLinkClasses('client-portal.dashboard')">
                                Dashboard
                            </a>
                            <a :href="route('client-portal.projects.index')" :class="navLinkClasses('client-portal.projects.index')">
                                Projects
                            </a>
                            <a :href="route('client-portal.tasks.index')" :class="navLinkClasses('client-portal.tasks.index')">
                                Tasks
                            </a>
                            <a :href="route('client-portal.sessions.index')" :class="navLinkClasses('client-portal.sessions.index')">
                                Sessions
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <user-menu :logout-url="route('client-portal.logout')"></user-menu>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main content -->
        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <slot></slot>
            </div>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import UserMenu from '@/Shared/UserMenu.vue'

const page = usePage()

const navLinkClasses = (routeName) => {
    const isActive = page.props.ziggy?.location?.route === routeName
    return isActive 
        ? 'border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium'
}
</script>


