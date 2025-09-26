<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Session Categories'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title">Session Categories</template>
        <template slot="top-right-toolbar">
            <button-link :href="route('session-category.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Session Category
            </button-link>
        </template>
        <div class="card" v-if="sessionCategories.length">
            <table class="table card-body">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Sessions</th>
                    <th><span class="float-right">Actions</span></th>
                </tr>
                <tr
                    v-for="sessionCategory in sessionCategories"
                    :key="sessionCategory.id"
                    class="item-container"
                >
                    <td>
                        <inertia-link :href="route('session-category.show', sessionCategory)">
                            {{ sessionCategory.name }}
                        </inertia-link>
                    </td>
                    <td>
                        <span class="text-gray-600">
                            {{ sessionCategory.description || 'No description' }}
                        </span>
                    </td>
                    <td>
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 inline-block">
                            {{ sessionCategory.sessions_count }} sessions
                        </span>
                    </td>
                    <td>
                        <div class="btn-group float-right">
                            <button-link
                                :href="route('session-category.edit', sessionCategory)"
                            >
                                <i class="fa fa-edit text-gray-600 text-xs"></i>
                            </button-link>
                            <delete-button
                                :url="route('session-category.destroy', sessionCategory.id)"
                            >
                                <i class="fa fa-trash text-gray-600 text-xs"></i>
                            </delete-button>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any session categories yet.
            <inertia-link :href="route('session-category.create')" class="text-blue-600 hover:text-blue-800 underline ml-1">
                Create your first session category
            </inertia-link>
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
        sessionCategories: Array,
    },
}
</script>