<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Clients'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Clients </template>
        <template #top-right-toolbar> 
            <button-link :href="route('client.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Client
            </button-link>
        </template>
        <div class="card item-type-container" data-item-type="client">
            <table class="table w-full card-body" v-if="clients.length">
                <thead>
                    <tr>
                        <th> Name </th>
                        <th> Email </th>
                        <th> <span class="float-right"> Actions </span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="client in clients"
                        :key="client.id"
                        class="item-container"
                        :data-item-name="client.name"
                        :data-item-destroy-route="route('client.destroy', client.id)"
                    >
                        <td>
                            <Link :href="route('client.show', client.id)">
                                {{ client.name }}
                            </Link>
                        </td>
                        <td> {{ client.email }} </td>
                        <td>
                            <div class="mx-auto btn-group float-right">
                                <button-link
                                    :href="route('client.edit', client.id)"
                                >
                                    <i class="fa fa-edit text-xs text-gray-600"></i>
                                </button-link>
                                <delete-button
                                    :url="route('client.destroy', client.id)"
                                >
                                    <i class="fa fa-trash text-xs text-gray-600"></i>
                                </delete-button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="card-body" v-else>
                You have not created any clients yet.
            </div>
        </div>
    </layout>
</template>

<script>
    import { Link } from '@inertiajs/vue3'
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import deleteButton from '@/Shared/DeleteButton.vue';
    import layout from '@/Shared/Layout.vue';

    export default {
        props: [
            'clients',
        ],
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
        },
    }
</script>
