<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Tasks'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> Tasks </template>
        <template slot="top-right-toolbar">
            <inertia-link :href="route('task.create')" class="btn btn-blue">
                <i class="fa fa-plus"></i>
                Create Task
            </inertia-link>
        </template>
        <div class="card" v-if="tasks.length">
            <table class="table card-body">
                <tr>
                    <th> Name </th>
                    <th> Parent </th>
                    <th> Project </th>
                    <th> Client </th>
                    <th> <span class="float-right"> Actions </span> </th>
                </tr>
                <tr
                    v-for="task in tasks"
                    class="item-container"
                >
                    <td>
                        <inertia-link :href="route('task.show', task)">
                            {{ task.shortName }}
                        </inertia-link>
                    </td>
                    <td>
                        <inertia-link :href="route('task.show', task.parent)" v-if="task.parent">
                            {{ task.parent.shortName }}
                        </inertia-link>
                    </td>
                    <td>
                        <inertia-link :href="route('project.show', task.project)" v-if="task.project">
                            {{ task.project.name }}
                        </inertia-link>
                    </td>
                    <td>
                        <inertia-link :href="route('client.show', task.project.client)" v-if="task.project && task.project.client">
                            {{ task.project.client.name }}
                        </inertia-link>
                    </td>
                    <td>
                        <div class="btn-group float-right">
                            <inertia-link
                                :href="route('task.edit', task)"
                                class="btn btn-default"
                            >
                                <i class="fa fa-edit"></i>
                            </inertia-link>
                            <button class="btn btn-default delete-item-button">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any tasks yet.
        </div>
    </layout>
</template>

<script>

    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'tasks',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
        }
    }

</script>