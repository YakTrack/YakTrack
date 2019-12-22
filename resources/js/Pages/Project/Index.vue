<template>
    <layout>
        <template slot="title"> Projects </template>
        <template slot="top-right-toolbar">
            <a :href="route('project.create')" class="btn btn-blue">
                <i class="fa fa-plus"></i>
                Create Project
            </a>
        </template>
        <div class="card">
            <table class="table card-body" v-if="projects.length">
                <tr>
                    <th> Name </th>
                    <th> Client </th>
                    <th> <span class="float-right"> Actions </span> </th>
                    <tr
                        v-for="project in projects"
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
                            <inertia-link
                                :href="route('project.edit', {project: project})"
                                class="btn btn-default"
                            >
                                <i class="fa fa-edit"></i>
                            </inertia-link>
                            <delete-button
                                :is-disabled="!project.isDeletable"
                                :url="route('project.destroy', project.id)"
                            >
                                <i class="fa fa-trash"></i>
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

import deleteButton from '@/Shared/DeleteButton';
import layout from '@/Shared/Layout';

export default {
    props: ['projects'],

    components: {
        deleteButton: deleteButton,
        layout: layout,
    },
}

</script>
