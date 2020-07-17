<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Sprints'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> Sprints </template>
        <template slot="top-right-toolbar"> 
            <button-link :href="route('sprint.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Sprint
            </button-link>
        </template>
        <div class="card item-type-container" data-item-type="sprint">
            <table class="table card-body" v-if="sprints.length">
                <tr>
                    <th> Name </th>
                    <th> Project </th>
                    <th> Status </th>
                    <th> <span class="float-right"> Actions </span> </th>
                    <tr
                        v-for="sprint in sprints"
                        :key="sprint.id"
                        class="item-container"
                        :data-item-name="sprint.name"
                        :data-item-destroy-route="route('sprint.destroy', sprint.id)"
                    >
                        <td>
                            <inertia-link :href="route('sprint.show', sprint.id)">
                                {{ sprint.name }}
                            </inertia-link>
                        </td>
                        <td>
                            <inertia-link :href="route('project.show', sprint.project_id)">
                                {{ sprint.project ? sprint.project.name : '' }}
                            </inertia-link>
                        </td>
                        <td>
                            <div class="text-green" v-if="sprint.is_open"> Open </div>
                        </td>
                        <td>
                            <div class="mx-auto btn-group float-right">
                                <button-link
                                    :href="route('sprint.edit', sprint.id)"
                                >
                                    <i class="fa fa-edit text-gray-600 text-xs"></i>
                                </button-link>
                                <delete-button
                                    :url="route('sprint.destroy', sprint.id)"
                                >
                                    <i class="fa fa-trash text-gray-600 text-xs"></i>
                                </delete-button>
                            </div>
                        </td>
                    </tr>
            </table>
            <div class="card-body" v-else>
                You have not created any sprints yet.
            </div>
        </div>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import deleteButton from '@/Shared/DeleteButton';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'sprints',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
        },
    }
</script>
