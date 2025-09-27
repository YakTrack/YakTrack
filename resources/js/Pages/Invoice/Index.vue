<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Invoices'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Invoices </template>
        <template #top-right-toolbar> 
            <button-link :href="route('invoice.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Invoice
            </button-link>
        </template>


        <div class="card">
            <table v-if="invoices.data && invoices.data.length" class="table card-body table-hover">
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
                        v-for="invoice in invoices.data"
                        :key="invoice.id"
                        class="item-container"
                    >
                        <td>
                            <Link :href="route('invoice.show', {id: invoice.id})">
                                {{ invoice.number }}
                            </Link>
                        </td>
                        <td> {{ invoice.date }} </td>
                        <td> {{ invoice.due_date }} </td>
                        <td>
                            <Link v-if="invoice.client" :href="route('client.show', {id: invoice.client_id})">
                                {{ invoice.client.name }}
                            </Link>
                        </td>
                        <td> {{ invoice.totalDurationForHumans == '0:00:00' ? '-' : invoice.totalDurationForHumans }} </td>
                        <td> {{ invoice.total_hours }} </td>
                        <td> {{ invoice.amountForHumans }} </td>
                        <td>
                            <div class="btn-group float-right">
                                <button-link
                                    :href="route('invoice.edit', invoice.id)"
                                >
                                    <i class="fa fa-edit text-xs text-gray-600"></i>
                                </button-link>
                                <delete-button
                                    :url="route('invoice.destroy', invoice.id)"
                                >
                                    <i class="fa fa-trash text-xs text-gray-600"></i>
                                </delete-button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-else class="">
                You have not created any invoices yet.
            </div>
            
            <div v-if="invoices.data && invoices.data.length" class="flex justify-center mt-4">
                <page-selector
                    :total="invoices.total"
                    :last-page="invoices.last_page"
                    :page="invoices.current_page"
                    :per-page="invoices.per_page"
                    :on-page-select="selectPage"
                >
                </page-selector>
            </div>
        </div>
    </layout>
</template>

<script>

import { Link } from '@inertiajs/vue3';
import breadcrumbs from '@/Shared/Breadcrumbs';
import deleteButton from '@/Shared/DeleteButton';
import layout from '@/Shared/Layout';
import pageSelector from '@/components/PageSelector';

export default {
    props: [
        'invoices',
    ],
    components: {
        Link,
        breadcrumbs: breadcrumbs,
        deleteButton: deleteButton,
        layout: layout,
        pageSelector: pageSelector,
    },
    methods: {
        selectPage(page) {
            this.$inertia.visit(route('invoice.index', {page: page}), {
                preserveScroll: true,
            });
        },
    },
}

</script>
