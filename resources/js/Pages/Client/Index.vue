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
                            <div class="float-right">
                                <actions-dropdown :options="getClientActions(client)" direction="left"></actions-dropdown>
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
    import actionsDropdown from '@/Shared/ActionsDropdown.vue';

    export default {
        props: [
            'clients',
        ],
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
            actionsDropdown: actionsDropdown,
        },
        methods: {
            getClientActions(client) {
                return [
                    {
                        name: 'Edit Client',
                        callback: () => {
                            this.$inertia.visit(route('client.edit', client.id));
                        }
                    },
                    {
                        name: 'Delete Client',
                        callback: () => {
                            if (confirm('Are you sure you want to delete this client?')) {
                                this.$inertia.delete(route('client.destroy', client.id));
                            }
                        }
                    }
                ];
            }
        },
    }
</script>
