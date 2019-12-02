<template>
    <layout>
        <template slot="title"> Invoices </template>
        <template slot="top-right-toolbar"> 
            <inertia-link :href="route('invoice.create')" class="btn btn-blue">
                <i class="fa fa-plus"></i>
                Create Invoice
            </inertia-link>
        </template>


        <div class="card">
            <table v-if="invoices.length" class="table card-body table-hover">
                <thead>
                    <tr>
                        <th> Number </th>
                        <th> Date </th>
                        <th> Due Date </th>
                        <th> Client </th>
                        <th> Session Hours </th>
                        <th> Invoiced Hours </th>
                        <th> Amount </th>
                        <th> <span class="float-right"> Actions </span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="invoice in invoices"
                        class="item-container"
                    >
                        <td>
                            <a :href="route('invoice.show', {id: invoice.id})">
                                {{ invoice.number }}
                            </a>
                        </td>
                        <td> {{ invoice.date }} </td>
                        <td> {{ invoice.due_date }} </td>
                        <td>
                            <a v-if="invoice.client" :href="route('client.show', {id: invoice.client_id})">
                                {{ invoice.client.name }}
                            </a>
                        </td>
                        <td> {{ invoice.totalDurationForHumans == '0:00:00' ? '-' : invoice.totalDurationForHumans }} </td>
                        <td> {{ invoice.total_hours }} </td>
                        <td> {{ invoice.amountForHumans }} </td>
                        <td>
                            <div class="btn-group float-right">
                                <inertia-link
                                    :href="route('invoice.edit', invoice.id)"
                                    class="btn btn-default"
                                >
                                    <i class="fa fa-edit"></i>
                                </inertia-link>
                                <delete-button
                                    :url="route('invoice.destroy', invoice.id)"
                                >
                                    <i class="fa fa-trash"></i>
                                </delete-button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="">
                You have not created any invoices yet.
            </div>
        </div>
    </layout>
</template>

<script>

import layout from '@/Shared/Layout';
import deleteButton from '@/Shared/DeleteButton';

export default {
    props: [
        'invoices',
    ],
    components: {
        layout: layout,
        deleteButton: deleteButton,
    },
}

</script>
