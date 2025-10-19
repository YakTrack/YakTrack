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
                            <div class="float-right">
                                <actions-dropdown :options="getTargetActions(target)" direction="left"></actions-dropdown>
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
    import actionsDropdown from '@/Shared/ActionsDropdown.vue';

    export default {
        props: [
            'targets',
        ],
        components: {
            Link,
            breadcrumbs: breadcrumbs,
            deleteButton: deleteButton,
            layout: layout,
            actionsDropdown: actionsDropdown,
        },
        methods: {
            getTargetActions(target) {
                return [
                    {
                        name: 'Edit Target',
                        callback: () => {
                            this.$inertia.visit(route('target.edit', target));
                        }
                    },
                    {
                        name: 'Delete Target',
                        callback: () => {
                            if (confirm('Are you sure you want to delete this target?')) {
                                this.$inertia.delete(route('target.destroy', target.id));
                            }
                        }
                    }
                ];
            }
        }
    }

</script>