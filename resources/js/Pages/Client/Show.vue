<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Clients',     url: route('client.index')},
                    {title: client.name},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> {{ client.name }} </template>
        <div class="card box-default">
            <div class="card-body">
                <div class="row">
                    <div class="col-xs-9">
                        <h2> {{ client.name }} </h2>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-9">
                        <h3> {{ client.email }} </h3>
                    </div>
                </div>
            </div>
        </div>
    </layout>
</template>

<script>
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';

    export default {
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
        },
        props: [
            'client',
        ],
        methods: {
        }
    }

</script>