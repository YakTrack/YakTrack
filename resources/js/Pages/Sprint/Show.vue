<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Sprints',     url: route('sprint.index')},
                    {title: sprint.name },
                ]"
            ></breadcrumbs>
        </template>
        <template #title> {{ sprint.name }} </template>
        <template #top-right-toolbar>
            <button @click="createInvoice" class="btn btn-blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Invoice
            </button>
        </template>
        <div class="card">
            <div>
                <h2 class="text-gray-700"> {{ sprint.name }} </h2>
            </div>
            <div class="mt-2">
                <span>
                    <Link :href="route('sprint.show', sprint)" class="no-underline text-xl text-gray-600">
                        {{ sprint.project.name }}
                    </Link>
                </span>
            </div>
            <div class="mt-2">
                <div class="font-mono text-lg"> {{ totalDurationForHumans }} </div>
                <div class="mt-1 text-gray-500"> Total time </div>
            </div>
        </div>

        <div class="rounded p-2 bg-white shadow mb-8 mt-4">
            <div class="p-2">
                <h3 class="text-gray-700"> Sprint Sessions </h3>
            </div>
            <table class="table rounded p-4 bg-white">
                <thead>
                    <tr>
                        <th class="text-gray-500"> Project </th>
                        <th class="text-gray-500"> Task </th>
                        <th class="text-gray-500"> Total Time </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in tasks" :key="task.id">
                        <td class="p-2 text-gray-600"><span v-if="task.project"> {{ task.project.name }} </span></td>
                        <td class="p-2 text-gray-600"> {{ task.name }} </td>
                        <td class="p-2 text-gray-600"> {{ task.totalDurationInSprintForHumans }} </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </layout>
</template>

<script>
    import { Link } from '@inertiajs/vue3';
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'sprint',
            'totalDurationForHumans',
            'tasks',
        ],
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            layout: layout,
        },
        methods: {
            createInvoice() {
                this.$inertia.post(route('sprint.invoice.store', this.sprint.id));
            }
        },
    }
</script>
