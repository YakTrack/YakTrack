<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Invoices',     url: route('invoice.index')},
                    {title: invoice.number},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Invoice {{ invoice.number }} </template>
        <template #top-right-toolbar> 
        </template>
        <div>
            <div class="card">
                <div class="flex">
                    <div class="p4 flex-1">
                        <i class="fa fa-user text-2xl text-gray-300 mr-2"></i>
                        <span class="text-2xl font-light"> {{ invoice.client.name }} </span>
                    </div>
                    <div class="p4">
                        <i class="fa fa-dollar-sign text-2xl text-gray-500 mr-4"></i>
                        <span class="text-2xl font-mono"> {{ invoice.amountForHumans }} </span>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="text-lg font-light"> {{ $filters.dateForHumans(invoice.date) }} </div>
                    <i class="fa fa-calendar-alt text-sm text-gray-300"></i>
                    <span class="text-sm font-light ml-1 text-gray-600"> Issued <span class="text-gray-500">{{ $filters.fromNow(invoice.date) }}</span></span>
                </div>
                <div class="mt-6">
                    <div class="text-lg font-light"> {{ $filters.dateForHumans(invoice.due_date) }} </div>
                    <i class="fa fa-calendar-check text-sm text-gray-300"></i>
                    <span class="text-sm font-light ml-1 text-gray-600"> Due <span class="text-gray-500">{{ $filters.fromNow(invoice.due_date) }}</span></span>
                </div>
                <div class="mt-6">
                    <div class="text-lg font-light"> {{ invoice.totalDurationForHumans }} </div>
                    <i class="fa fa-stopwatch text-sm text-gray-300"></i>
                    <span class="text-sm font-light ml-1 text-gray-600"> Total Time <span class="text-gray-500"> of Linked Sessions </span></span>
                </div>
            </div>
        </div>
        <div class="mt-8">
            <h2 class="mb-4"> Linked Sessions </h2>
            <session-table
                :sessions="invoice.sessions"
                :hide-invoice-column="true"
            ></session-table>
        </div>
    </layout>
</template>

<script>

import breadcrumbs from '@/Shared/Breadcrumbs';
import dropdown from '@/Shared/Dropdown';
import layout from '@/Shared/Layout';
import sessionTable from '@/Shared/SessionTable';

export default {
    props: [
        'invoice',
    ],
    components: {
        breadcrumbs: breadcrumbs,
        dropdown: dropdown,
        layout: layout,
        sessionTable: sessionTable,
    },
}

</script>

