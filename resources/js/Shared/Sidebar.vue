<template>
    <div class="bg-gray-200 px-4">
        <nav class="px-6 pb-6 pt-24 overflow-y-auto text-base lg:text-lg lg:px-0 sticky?lg:h-(screen-16) leading-loose">
            <ul
                class="list-reset"
                v-for="menuItem in menuItems"
                :key="menuItem.name"
            >
                <li class="lg:mb-2 hover:bg-blue-400 px-6 py-1 rounded" :class="menuItem.active ? 'bg-grey-300' : ''">
                    <inertia-link
                        class="no-underline text-gray-900"
                        :href="route(menuItem.route)"
                    >
                        <i class="text-gray-500 parent-hover:text-white" :class="menuItem.icon"></i>
                        <span class="ml-3 parent-hover:text-white"> {{ menuItem.name }} </span>
                    </inertia-link>
                </li>
            </ul>
        </nav>
    </div>
</template>

<script>
    export default {
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
                        icon: 'fas fa-briefcase fa-fw'
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
