<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Clients'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> Clients </template>
        <template slot="top-right-toolbar"> 
            <inertia-link :href="route('client.create')" class="btn btn-blue">
                <i class="fa fa-plus"></i>
                Create Client
            </inertia-link>
        </template>
        <div class="card item-type-container" data-item-type="client">
            <table class="table card-body" v-if="clients.length">
                <tr>
                    <th> Name </th>
                    <th> Email </th>
                    <th> <span class="float-right"> Actions </span> </th>
                    <tr
                        v-for="client in clients"
                        class="item-container"
                        :data-item-name="client.name"
                        :data-item-destroy-route="route('client.destroy', client.id)"
                    >
                        <td>
                            <inertia-link :href="route('client.show', client.id)">
                                {{ client.name }}
                            </inertia-link>
                        </td>
                        <td> {{ client.email }} </td>
                        <td>
                            <div class="mx-auto btn-group float-right">
                                <inertia-link
                                    :href="route('client.edit', client.id)"
                                    class="btn btn-default"
                                >
                                    <i class="fa fa-edit"></i>
                                </inertia-link>
                                <delete-button
                                    :url="route('client.destroy', client.id)"
                                >
                                    <i class="fa fa-trash"></i>
                                </delete-button>
                            </div>
                        </td>
                    </tr>
            </table>
            <div class="card-body" v-else>
                You have not created any clients yet.
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
            'clients',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
        },
    }
</script>
