<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home', url: route('home')},
                    {title: 'Session Categories'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title>Session Categories</template>
        <template #top-right-toolbar>
            <button-link :href="route('session-category.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Session Category
            </button-link>
        </template>
        <div class="card" v-if="sessionCategories.length">
            <table class="table card-body">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Sessions</th>
                        <th><span class="float-right">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="sessionCategory in sessionCategories"
                        :key="sessionCategory.id"
                        class="item-container"
                    >
                        <td>
                            <Link :href="route('session-category.show', sessionCategory)">
                                {{ sessionCategory.name }}
                            </Link>
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
                            <div class="float-right">
                                <actions-dropdown :options="getSessionCategoryActions(sessionCategory)" direction="left"></actions-dropdown>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any session categories yet.
            <Link :href="route('session-category.create')" class="text-blue-600 hover:text-blue-800 underline ml-1">
                Create your first session category
            </Link>
        </div>
    </layout>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import Breadcrumbs from '@/Shared/Breadcrumbs.vue'
import ButtonLink from '@/Shared/ButtonLink.vue'
import DeleteButton from '@/Shared/DeleteButton.vue'
import ActionsDropdown from '@/Shared/ActionsDropdown.vue'

export default {
    components: {
        Link,
        Layout,
        Breadcrumbs,
        ButtonLink,
        DeleteButton,
        ActionsDropdown,
    },
    methods: {
        getSessionCategoryActions(sessionCategory) {
            return [
                {
                    name: 'Edit Category',
                    callback: () => {
                        this.$inertia.visit(route('session-category.edit', sessionCategory));
                    }
                },
                {
                    name: 'Delete Category',
                    callback: () => {
                        if (confirm('Are you sure you want to delete this session category?')) {
                            this.$inertia.delete(route('session-category.destroy', sessionCategory.id));
                        }
                    }
                }
            ];
        }
    },
    props: {
        sessionCategories: Array,
    },
}
</script>