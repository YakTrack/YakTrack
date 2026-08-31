<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',         url: route('home')},
                    {title: 'Clients',     url: route('client.index')},
                    {title: client.name},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> {{ client.name }} </template>
        <template #top-right-toolbar>
            <button
                v-if="isFocused"
                type="button"
                class="btn btn-white"
                @click="clearFocus"
            >
                Clear focus
            </button>
            <button
                v-else
                type="button"
                class="btn btn-blue"
                @click="focusOnClient"
            >
                Focus on this client
            </button>
        </template>
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
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import layout from '@/Shared/Layout.vue';

    export default {
        components: {
            breadcrumbs: breadcrumbs,
            layout: layout,
        },
        props: [
            'client',
        ],
        computed: {
            isFocused() {
                const focusedClient = this.$page.props.focusedClient;

                return focusedClient != null && focusedClient.id === this.client.id;
            },
        },
        methods: {
            focusOnClient() {
                this.$inertia.patch(route('focused-client.update'), { client_id: this.client.id }, {
                    preserveScroll: true,
                });
            },
            clearFocus() {
                this.$inertia.patch(route('focused-client.update'), { client_id: null }, {
                    preserveScroll: true,
                });
            },
        }
    }

</script>