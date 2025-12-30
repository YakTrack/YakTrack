<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs :breadcrumbs="[
                {title: 'Home', url: route('home')},
                {title: 'Features', url: route('features.index')},
                {title: feature.name},
            ]"></breadcrumbs>
        </template>
        <template #title>
            <span v-if="feature.code" class="font-mono bg-gray-100 px-2 py-1 rounded text-sm mr-2">
                {{ feature.code }}
            </span>
            {{ feature.name }}
        </template>
        <template #top-right-toolbar>
            <button-link :href="route('features.edit', feature.id)" color="blue">
                <i class="fa fa-edit text-blue-100 mr-2"></i>
                Edit Feature
            </button-link>
        </template>

        <div class="card">
            <div class="card-body">
                <div v-if="feature.project" class="mb-4">
                    <i class="fa fa-briefcase text-2xl text-gray-300 mr-2"></i>
                    <Link :href="route('project.show', feature.project)" class="text-2xl font-light">
                        {{ feature.project.name }}
                    </Link>
                </div>
                <p v-if="feature.description" class="mt-4">
                    {{ feature.description }}
                </p>
            </div>
        </div>

        <!-- Acceptance Criteria -->
        <div class="card mt-4">
            <div class="card-header">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-medium text-gray-dark">Acceptance Criteria</h2>
                    <div class="flex items-center gap-3">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ feature.acceptance_criteria.length }} criteria
                        </span>
                        <button-link
                            :href="createAcceptanceCriteriaUrl"
                            color="blue"
                        >
                            <i class="fa fa-plus mr-2"></i>
                            Create Acceptance Criteria
                        </button-link>
                    </div>
                </div>
            </div>
            <div class="card-body">
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
                                <button-link
                                    :href="route('acceptance-criteria.edit', criteria.id)"
                                    class="text-gray-500 hover:text-gray-700"
                                >
                                    <i class="fa fa-edit"></i>
                                </button-link>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-else class="text-center py-8 text-gray-500">
                    <i class="fa fa-clipboard-list text-4xl mb-4"></i>
                    <p class="text-lg mb-2">No acceptance criteria found for this feature.</p>
                    <button-link
                        :href="createAcceptanceCriteriaUrl"
                        color="blue"
                    >
                        <i class="fa fa-plus mr-2"></i>
                        Create Acceptance Criteria
                    </button-link>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import layout from '@/Shared/Layout.vue'
import buttonLink from '@/Shared/ButtonLink.vue'

export default {
    props: [
        'feature',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        layout: layout,
        buttonLink: buttonLink,
    },
    computed: {
        createAcceptanceCriteriaUrl() {
            const baseUrl = route('acceptance-criteria.create');
            const params = new URLSearchParams();
            
            if (this.feature.project_id) {
                params.append('project_id', this.feature.project_id);
            }
            if (this.feature.id) {
                params.append('feature_id', this.feature.id);
            }
            
            const queryString = params.toString();
            return queryString ? `${baseUrl}?${queryString}` : baseUrl;
        },
    },
}
</script>
