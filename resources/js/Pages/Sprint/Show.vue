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
                <h2 class="text-gray-700 dark:text-gray-300"> {{ sprint.name }} </h2>
            </div>
            <div class="mt-2">
                <span v-if="sprint.projects && sprint.projects.length">
                    <template v-for="(project, index) in sprint.projects" :key="project.id">
                        <Link :href="route('project.show', project.id)" class="no-underline text-xl text-gray-600 dark:text-gray-300">{{ project.name }}</Link><span v-if="index < sprint.projects.length - 1">, </span>
                    </template>
                </span>
            </div>
            <div class="mt-2">
                <div class="font-mono text-lg"> {{ totalDurationForHumans }} </div>
                <div class="mt-1 text-gray-500 dark:text-gray-400"> Total time </div>
            </div>
        </div>

        <div class="rounded p-2 bg-white shadow mb-8 mt-4 dark:bg-gray-800">
            <div class="p-2">
                <h3 class="text-gray-700 dark:text-gray-300"> Sprint Sessions </h3>
            </div>
            <table class="table rounded p-4 bg-white dark:bg-gray-800">
                <thead>
                    <tr>
                        <th class="text-gray-500 dark:text-gray-400"> Project </th>
                        <th class="text-gray-500 dark:text-gray-400"> Task </th>
                        <th class="text-gray-500 dark:text-gray-400"> Total Time </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in tasks" :key="task.id">
                        <td class="p-2 text-gray-600 dark:text-gray-300"><span v-if="task.project"> {{ task.project.name }} </span></td>
                        <td class="p-2 text-gray-600 dark:text-gray-300"> {{ task.name }} </td>
                        <td class="p-2 text-gray-600 dark:text-gray-300"> {{ task.totalDurationInSprintForHumans }} </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </layout>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import breadcrumbs from '@/Shared/Breadcrumbs.vue';
import layout from '@/Shared/Layout.vue';

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
        },
        durationForHumans(seconds) {
            return durationForHumans(seconds)
        },
    },
}
</script>
