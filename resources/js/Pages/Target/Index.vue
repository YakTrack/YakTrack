<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Targets'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> Targets </template>
        <template slot="top-right-toolbar">
            <button-link :href="route('target.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Target
            </button-link>
        </template>
        <div class="card" v-if="targets.length">
            <table class="table card-body">
                <tr>
                    <th> Date </th>
                    <th> Target </th>
                    <th> <span class="float-right"> Actions </span> </th>
                </tr>
                <tr
                    v-for="target in targets"
                    :key="target.id"
                    class="item-container"
                >
                    <td>
                        <inertia-link :href="route('target.show', target)">
                            {{ target.starts_at | dateForHumans }}
                        </inertia-link>
                    </td>
                    <td>
                        <inertia-link :href="route('target.show', target)">
                            {{ target.value }} {{ target.value_unit }}
                        </inertia-link>
                    </td>
                    <td>
                        <div class="btn-group float-right">
                            <button-link
                                :href="route('target.edit', target)"
                            >
                                <i class="fa fa-edit text-gray-600 text-xs"></i>
                            </button-link>
                            <delete-button
                                :url="route('target.destroy', target.id)"
                            >
                                <i class="fa fa-trash text-gray-600 text-xs"></i>
                            </delete-button>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any targets yet.
        </div>
    </layout>
</template>

<script>

    import breadcrumbs from '@/Shared/Breadcrumbs';
    import deleteButton from '@/Shared/DeleteButton';
    import layout from '@/Shared/Layout';

    export default {
        props: [
            'targets',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
        }
    }

</script>