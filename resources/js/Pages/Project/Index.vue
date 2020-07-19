<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Projects'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> Projects </template>
        <template slot="top-right-toolbar">
            <button-link :href="route('project.create')" color="blue">
                <i class="fa fa-plus mr-2 text-blue-100"></i>
                Create Project
            </button-link>
        </template>
        <div class="card">
            <table class="table card-body" v-if="projects.length">
                <tr>
                    <th> Name </th>
                    <th> Client </th>
                    <th> <span class="float-right"> Actions </span> </th>
                    <tr
                        v-for="project in projects"
                        :key="project.id"
                        class="item-container"
                    >
                    <td>
                        <inertia-link :href="route('project.show', {project: project.id})">
                            {{ project.name }}
                        </inertia-link>
                    </td>
                    <td>
                        <inertia-link
                            v-if="project.client"
                            :href="route('client.show', {client: project.client.id})">
                            {{ project.client.name }}
                        </inertia-link>
                    </td>
                    <td>
                        <div class="btn-group float-right">
                            <button-link
                                :href="route('project.edit', {project: project})"
                            >
                                <i class="fa fa-edit text-xs text-gray-600"></i>
                            </button-link>
                            <delete-button
                                :is-disabled="!project.isDeletable"
                                :url="route('project.destroy', project.id)"
                            >
                                <i class="fa fa-trash text-xs text-gray-600"></i>
                            </delete-button>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="card-body" v-else>
                You have not created any projects yet.
            </div>
        </div>
    </layout>
</template>

<script>

import breadcrumbs from '@/Shared/Breadcrumbs';
import deleteButton from '@/Shared/DeleteButton';
import layout from '@/Shared/Layout';

export default {
    props: ['projects'],

    components: {
        breadcrumbs: breadcrumbs,
        deleteButton: deleteButton,
        layout: layout,
    },
}

</script>
