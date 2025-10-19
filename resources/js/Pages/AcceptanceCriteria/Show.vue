<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Acceptance Criteria', url: route('acceptance-criteria.index')},
                    {title: criteria.name},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>{{ criteria.name }}</template>
        <template #top-right-toolbar>
            <div class="flex space-x-2">
                <button-link :href="route('acceptance-criteria.edit', criteria.id)" color="yellow">
                    <i class="fa fa-edit text-yellow-100 mr-2"></i>
                    Edit
                </button-link>
                <button-link :href="route('acceptance-criteria.index')" color="gray">
                    <i class="fa fa-arrow-left text-gray-100 mr-2"></i>
                    Back to List
                </button-link>
            </div>
        </template>

        <!-- Criteria Info -->
        <div class="card mb-6">
            <div class="card-body">
                <div class="flex items-center space-x-4">
                    <span v-if="criteria.code" class="font-mono bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded text-sm">
                        {{ criteria.code }}
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ criteria.project.name }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Main Content -->
            <div class="space-y-6">
                <!-- Description -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Description</h2>
                    </div>
                    <div class="card-body">
                        <div v-if="criteria.description" class="prose dark:prose-invert max-w-none">
                            <p class="whitespace-pre-wrap">{{ criteria.description }}</p>
                        </div>
                        <p v-else class="text-gray-500 dark:text-gray-400 italic">No description provided</p>
                    </div>
                </div>

                <!-- Linked Tasks -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Linked Tasks</h2>
                    </div>
                    <div class="card-body">
                        <div v-if="criteria.tasks.length > 0" class="space-y-2">
                            <div
                                v-for="task in criteria.tasks"
                                :key="task.id"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                            >
                                <div>
                                    <Link
                                        :href="route('task.show', task.id)"
                                        class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400"
                                    >
                                        {{ task.name }}
                                    </Link>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ task.project.name }}</p>
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 dark:text-gray-400 italic">No tasks linked to this criteria</p>
                    </div>
                </div>

                <!-- Test Results History -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Test Results History</h2>
                    </div>
                    <div class="card-body">
                        <div v-if="criteria.test_results.length > 0" class="space-y-3">
                            <div
                                v-for="result in criteria.test_results"
                                :key="result.id"
                                class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg"
                            >
                                <div class="flex items-center space-x-3">
                                    <span
                                        :class="getStatusBadgeClass(result.status)"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    >
                                        {{ result.status }}
                                    </span>
                                    <div>
                                        <Link
                                            :href="route('test-run.show', result.test_run.id)"
                                            class="font-medium text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400"
                                        >
                                            {{ result.test_run.name }}
                                        </Link>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ formatDate(result.test_run.executed_at) }} by {{ result.test_run.executed_by_user.name }}
                                        </p>
                                    </div>
                                </div>
                                <div v-if="result.notes" class="text-sm text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                    {{ result.notes }}
                                </div>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 dark:text-gray-400 italic">No test results yet</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Version History -->
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Version History</h2>
                    </div>
                    <div class="card-body">
                        <div class="space-y-4">
                            <div
                                v-for="version in criteria.versions"
                                :key="version.id"
                                class="border-l-4 border-blue-500 pl-4"
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        Version {{ version.version_number }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ formatDate(version.changed_at) }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    Changed by {{ version.changed_by_user.name }}
                                </p>
                                <div v-if="version.code !== criteria.code" class="mt-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Code: {{ version.code || 'No code' }}</p>
                                </div>
                                <div v-if="version.name !== criteria.name" class="mt-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Name: {{ version.name }}</p>
                                </div>
                                <div v-if="version.description !== criteria.description" class="mt-2">
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Description: {{ version.description || 'No description' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'

defineProps({
  criteria: Object,
})

const getStatusBadgeClass = (status) => {
  const classes = {
    passed: 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    failed: 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
    skipped: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    blocked: 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
  }
  return classes[status] || classes.skipped
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}
</script>


