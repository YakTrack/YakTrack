<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Tasks'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Tasks </template>
        <template #top-right-toolbar>
            <button-link :href="route('task.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Task
            </button-link>
        </template>
        <div class="card" v-if="tasks.length">
            <table class="table card-body">
                <thead>
                    <tr>
                        <th> Name </th>
                        <th> Parent </th>
                        <th> Project </th>
                        <th> Client </th>
                        <th> Status </th>
                        <th> <span class="float-right"> Actions </span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="task in tasks"
                        class="item-container"
                    >
                        <td>
                            <Link :href="route('task.show', task)">
                                {{ task.shortName }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('task.show', task.parent)" v-if="task.parent">
                                {{ task.parent.shortName }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('project.show', task.project)" v-if="task.project">
                                {{ task.project.name }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('client.show', task.project.client)" v-if="task.project && task.project.client">
                                {{ task.project.client.name }}
                            </Link>
                        </td>
                        <td>
                            <span v-if="task.task_status"
                                  :style="{ backgroundColor: task.task_status.color }"
                                  class="px-2 py-1 text-xs rounded-full text-white inline-block">
                                {{ task.task_status.name }}
                            </span>
                            <span v-else class="px-2 py-1 text-xs rounded-full bg-gray-300 text-gray-700 inline-block">
                                No Status
                            </span>
                        </td>
                        <td>
                            <div class="btn-group float-right">
                                <button-link
                                    :href="route('task.edit', task)"
                                >
                                    <i class="fa fa-edit text-gray-600 text-xs"></i>
                                </button-link>
                                <delete-button
                                    :url="route('task.destroy', task.id)"
                                >
                                    <i class="fa fa-trash text-gray-600 text-xs"></i>
                                </delete-button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any tasks yet.
        </div>
    </layout>
</template>

<script>

    import { Link } from '@inertiajs/vue3';
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import deleteButton from '@/Shared/DeleteButton.vue';
    import layout from '@/Shared/Layout.vue';

    export default {
        props: [
            'tasks',
        ],
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
        }
    }

</script>