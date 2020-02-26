<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Sprints',     url: route('sprint.index')},
                    {title: sprint.name },
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> {{ sprint.name }} </template>
        <div class="card">
            <div>
                <h2 class="text-grey-darker"> {{ sprint.name }} </h2>
            </div>
            <div class="mt-2">
                <span>
                    <inertia-link :href="route('sprint.show', sprint)" class="no-underline text-xl text-grey-dark">
                        {{ sprint.project.name }}
                    </inertia-link>
                </span>
            </div>
            <div class="mt-2">
                <div class="font-mono text-lg"> {{ totalDurationForHumans }} </div>
                <div class="mt-1 text-grey"> Total time </div>
            </div>
        </div>

        <div class="rounded p-2 bg-white shadow mb-8 mt-4">
            <div class="p-2">
                <h3 class="text-grey-darker"> Sprint Sessions </h3>
            </div>
            <table class="table rounded p-4 bg-white">
                <tr>
                    <th class="text-grey"> Project </th>
                    <th class="text-grey"> Task </th>
                    <th class="text-grey"> Total Time </th>
                </tr>
                <tr v-for="task in tasks">
                    <td class="p-2 text-grey-dark"><span v-if="task.project"> {{ task.project.name }} </span></td>
                    <td class="p-2 text-grey-dark"> {{ task.name }} </td>
                    <td class="p-2 text-grey-dark"> {{ task.totalDurationInSprintForHumans }} </td>
                </tr>
            </table>
        </div>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'sprint',
            'totalDurationForHumans',
            'tasks',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
        },
    }
</script>
