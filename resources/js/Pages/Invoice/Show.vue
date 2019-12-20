<template>
    <layout>
        <template slot="title"> Invoice {{ invoice.number }} </template>
        <template slot="top-right-toolbar"> 
        </template>
        <div>
            <div class="card">
                <div class="flex">
                    <div class="p4 flex-1">
                        <i class="fa fa-user text-2xl text-grey-light mr-2"></i>
                        <span class="text-2xl font-light"> {{ invoice.client.name }} </span>
                    </div>
                    <div class="p4">
                        <i class="fa fa-dollar-sign text-2xl text-grey mr-4"></i>
                        <span class="text-2xl font-mono"> {{ invoice.amountForHumans }} </span>
                    </div>
                </div>
                <div class="mt-6">
                    <div class="text-lg font-light"> {{ invoice.date | dateForHumans }} </div>
                    <i class="fa fa-calendar-alt text-sm text-grey-light"></i>
                    <span class="text-sm font-light ml-1 text-grey-dark"> Issued <span class="text-grey">{{ invoice.date | fromNow }}</span></span>
                </div>
                <div class="mt-6">
                    <div class="text-lg font-light"> {{ invoice.due_date | dateForHumans }} </div>
                    <i class="fa fa-calendar-check text-sm text-grey-light"></i>
                    <span class="text-sm font-light ml-1 text-grey-dark"> Due <span class="text-grey">{{ invoice.due_date | fromNow }}</span></span>
                </div>
                <div class="mt-6">
                    <div class="text-lg font-light"> {{ invoice.totalDurationForHumans }} </div>
                    <i class="fa fa-stopwatch text-sm text-grey-light"></i>
                    <span class="text-sm font-light ml-1 text-grey-dark"> Total Time <span class="text-grey"> of Linked Sessions </span></span>
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

import layout from '@/Shared/Layout';
import sessionTable from '@/Shared/SessionTable';
import dropdown from '@/Shared/Dropdown';

export default {
    props: [
        'invoice',
    ],
    components: {
        dropdown: dropdown,
        layout: layout,
        sessionTable: sessionTable,
    },
}

</script>

