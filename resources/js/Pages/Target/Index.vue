<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home',     url: route('home')},
                    {title: 'Targets'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Targets </template>
        <template #top-right-toolbar>
            <button-link :href="route('target.create')" color="blue">
                <i class="fa fa-plus text-blue-100 mr-2"></i>
                Create Target
            </button-link>
        </template>
        <div class="card" v-if="targets.length">
            <table class="table card-body">
                <thead>
                    <tr>
                        <th> Date </th>
                        <th> Target </th>
                        <th> <span class="float-right"> Actions </span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="target in targets"
                        :key="target.id"
                        class="item-container"
                    >
                        <td>
                            <Link :href="route('target.show', target)">
                                {{ $filters.dateForHumans(target.starts_at) }}
                            </Link>
                        </td>
                        <td>
                            <Link :href="route('target.show', target)">
                                {{ target.value }} {{ target.value_unit }}
                            </Link>
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
                </tbody>
            </table>
        </div>
        <div class="card-body" v-else>
            You have not created any targets yet.
        </div>
    </layout>
</template>

<script>

    import { Link } from '@inertiajs/vue3';
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import deleteButton from '@/Shared/DeleteButton.vue';
    import layout from '@/Shared/Layout.vue';

    export default {
        props: [
            'targets',
        ],
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
        }
    }

</script>