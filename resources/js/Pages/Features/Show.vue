<template>
    <layout>
        <breadcrumbs :items="breadcrumbs" />

        <!-- Header -->
        <div class="card-header">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ feature.name }}</h1>
                    <p class="text-gray-600 mt-1">
                        <Link :href="route('project.show', feature.project)" v-if="feature.project">
                            {{ feature.project.name }}
                        </Link>
                    </p>
                </div>
                <div class="flex gap-2">
                    <Link
                        :href="route('features.edit', feature.id)"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Edit Feature
                    </Link>
                    <Link
                        :href="route('features.index')"
                        class="px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                    >
                        Back to Features
                    </Link>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="card-body" v-if="feature.description">
            <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
            <p class="text-gray-600">{{ feature.description }}</p>
        </div>

        <!-- Acceptance Criteria -->
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Acceptance Criteria</h3>
                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                    {{ feature.acceptance_criteria.length }} criteria
                </span>
            </div>

            <div v-if="feature.acceptance_criteria.length" class="space-y-4">
                <div
                    v-for="criteria in feature.acceptance_criteria"
                    :key="criteria.id"
                    class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
                >
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <span v-if="criteria.code" class="font-mono bg-gray-100 px-2 py-1 rounded text-xs">
                                    {{ criteria.code }}
                                </span>
                                <Link
                                    :href="route('acceptance-criteria.show', criteria.id)"
                                    class="text-lg font-medium text-gray-900 hover:text-blue-600"
                                >
                                    {{ criteria.name }}
                                </Link>
                            </div>
                            <p v-if="criteria.description" class="text-gray-600 text-sm">
                                {{ criteria.description }}
                            </p>
                        </div>
                        <div class="flex gap-2 ml-4">
                            <Link
                                :href="route('acceptance-criteria.edit', criteria.id)"
                                class="text-gray-400 hover:text-gray-600"
                            >
                                <i class="fa fa-edit text-xs"></i>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="text-center py-8 text-gray-500">
                <i class="fa fa-clipboard-list text-4xl mb-4"></i>
                <p>No acceptance criteria found for this feature.</p>
                <Link
                    :href="route('acceptance-criteria.create')"
                    class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"
                >
                    Create Acceptance Criteria
                </Link>
            </div>
        </div>
    </layout>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'

export default {
    props: [
        'feature',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        layout: layout,
    },
    computed: {
        breadcrumbs() {
            return [
                { label: 'Features', href: route('features.index') },
                { label: this.feature.name, href: route('features.show', this.feature.id) },
            ]
        },
    },
}
</script>
