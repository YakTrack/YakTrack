<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Session Categories', url: route('session-category.index')},
                    {title: sessionCategory.name},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title">{{ sessionCategory.name }}</template>
        <template slot="top-right-toolbar">
            <button-link :href="route('session-category.edit', sessionCategory)" color="blue">
                <i class="fa fa-edit text-blue-100 mr-2"></i>
                Edit Category
            </button-link>
        </template>

        <div class="card">
            <div class="card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-semibold mb-3">Category Details</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Name</label>
                                <p class="text-gray-900">{{ sessionCategory.name }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-600">Description</label>
                                <p class="text-gray-900">
                                    {{ sessionCategory.description || 'No description provided' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-3">Statistics</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="text-sm font-medium text-gray-600">Total Sessions</label>
                                <p class="text-2xl font-bold text-blue-600">{{ sessionCategory.sessions_count }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6" v-if="sessionCategory.sessions_count > 0">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold">Sessions in this Category</h3>
                </div>
                <div class="card-body">
                    <p class="text-gray-600">
                        This category has {{ sessionCategory.sessions_count }} associated session(s).
                        <inertia-link :href="route('session.index', { session_category_id: sessionCategory.id })"
                                      class="text-blue-600 hover:text-blue-800 underline ml-1">
                            View all sessions in this category
                        </inertia-link>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-between items-center">
            <button-link :href="route('session-category.index')" color="gray">
                <i class="fa fa-arrow-left mr-2"></i>
                Back to Categories
            </button-link>

            <delete-button
                :url="route('session-category.destroy', sessionCategory.id)"
                class="btn btn-red"
            >
                <i class="fa fa-trash mr-2"></i>
                Delete Category
            </delete-button>
        </div>
    </layout>
</template>

<script>
import Layout from '../../Shared/Layout.vue'
import Breadcrumbs from '../../Shared/Breadcrumbs.vue'
import ButtonLink from '../../Shared/ButtonLink.vue'
import DeleteButton from '../../Shared/DeleteButton.vue'

export default {
    components: {
        Layout,
        Breadcrumbs,
        ButtonLink,
        DeleteButton,
    },
    props: {
        sessionCategory: Object,
    },
}
</script>