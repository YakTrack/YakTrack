<template>
    <div class="bg-gray-200 pl-4">
        <nav class="px-6 pb-6 pt-24 overflow-y-auto text-base lg:text-lg lg:pl-0 lg:pr-4 sticky?lg:h-(screen-16) leading-loose">
            <ul
                class="list-reset"
                v-for="(menuItem, index) in menuItems"
                :key="index"
            >
                <li class="lg:mb-2 hover:bg-blue-400 px-6 py-1 rounded" :class="menuItem.active ? 'bg-gray-300' : ''">
                    <Link
                        class="no-underline text-gray-900"
                        :href="route(menuItem.route)"
                    >
                        <i class="text-gray-500 parent-hover:text-white" :class="menuItem.icon"></i>
                        <span class="ml-3 parent-hover:text-white"> {{ menuItem.name }} </span>
                    </Link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    import { Link } from '@inertiajs/vue3';

    export default {
        components: {
            Link,
        },
        data() {
            return {
                menuItems: [
                    {
                        name: 'Home',
                        route: 'home',
                        icon: 'fas fa-tachometer-alt fa-fw',
                        active: route().current('home'),
                    },
                    {
                        name: 'Sessions',
                        route: 'session.index',
                        icon: 'fas fa-stopwatch fa-fw',
                        active: route().current('session.*'),
                    },
                    {
                        name: 'Categories',
                        route: 'session-category.index',
                        icon: 'fas fa-tags fa-fw',
                        active: route().current('session-category.*'),
                    },
                    {
                        name: 'Tasks',
                        route: 'task.index',
                        icon: 'fas fa-check-square fa-fw',
                        active: route().current('task.*'),
                    },
                    {
                        name: 'Sprints',
                        route: 'sprint.index',
                        icon: 'fas fa-calendar-times fa-fw',
                        active: route().current('sprint.*'),
                    },
                    {
                        name: 'Projects',
                        route: 'project.index',
                        icon: 'fas fa-briefcase fa-fw',
                        active: route().current('project.*'),
                    },
                    {
                        name: 'Acceptance Criteria',
                        route: 'acceptance-criteria.index',
                        icon: 'fas fa-clipboard-check fa-fw',
                        active: route().current('acceptance-criteria.*'),
                    },
                    {
                        name: 'Test Runs',
                        route: 'test-run.index',
                        icon: 'fas fa-vial fa-fw',
                        active: route().current('test-run.*'),
                    },
                    {
                        name: 'Targets',
                        route: 'target.index',
                        icon: 'fas fa-bullseye fa-fw',
                        active: route().current('target.*'),
                    },
                    {
                        name: 'Invoices',
                        route: 'invoice.index',
                        icon: 'fas fa-file-invoice-dollar fa-fw',
                        active: route().current('invoice.*'),
                    },
                    {
                        name: 'Clients',
                        route: 'client.index',
                        icon: 'fas fa-users fa-fw',
                        active: route().current('client.*'),
                    },
                ]
            };
        }
    }

</script>
