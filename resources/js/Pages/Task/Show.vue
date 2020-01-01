<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs :breadcrumbs="[
                {title: 'Home',     url: route('home')},
                {title: 'Tasks',    url: route('task.index')},
                {title: task.name},
            ]" ></breadcrumbs>
        </template>
        <template slot="title"> {{ task.name }} </template>
        <div class="card">
            <h1 class="font-medium text-gray-dark"> {{ task.name }} </h1>
            <div class="p4 mt-4" v-if="task.project">
                <i class="fa fa-briefcase text-2xl text-grey-light mr-2"></i>
                <span class="text-2xl font-light"> {{ task.project.name }} </span>
            </div>
            <div class="p4 mt-4" v-if="task.project && task.project.client">
                <i class="fa fa-users text-2xl text-grey-light mr-2"></i>
                <span class="text-2xl font-light"> {{ task.project.client.name }} </span>
            </div>
            <p class="mt-4">
                {{ task.description }}
            </p>
        </div>
        <session-table
            class="mt-6"
            :sessions="task.sessions"
            :hide-linked-to-column="true"
            :hide-invoice-column="true"
        ></session-table>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';
    import sessionTable from '@/Shared/SessionTable';

    export default {
        props: [
            'task',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
            sessionTable: sessionTable,
        }
    }

</script>
