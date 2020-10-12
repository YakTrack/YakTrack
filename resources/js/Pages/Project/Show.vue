<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs :breadcrumbs="[
                {title: 'Home',         url: route('home')},
                {title: 'Projects',     url: route('project.index')},
                {title: project.name},
            ]" ></breadcrumbs>
        </template>
        <div class="card">
            <h1 class="font-medium text-gray-dark"> {{ project.name }} </h1>
            <div class="p4 mt-4">
                <i class="fa fa-user text-2xl text-gray-300 mr-2"></i>
                <span class="text-2xl font-light"> {{ project.client.name }} </span>
            </div>
            <p class="mt-4">
                {{ project.description }}
            </p>
        </div>
    </layout>
</template>

<script>

    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'project',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
        }
    }

</script>
