<template>
    <div class="mt-6 flex flex-wrap gap-2 border-t border-gray-200 pt-4 dark:border-gray-700">
        <Link
            :href="tabUrl('overview')"
            class="px-4 py-2 rounded text-sm border transition"
            :class="isActive('overview')
                ? activeClass
                : inactiveClass"
        >
            <i class="fa fa-th-large mr-1"></i>
            Overview
        </Link>
        <Link
            :href="tabUrl('tasks')"
            class="px-4 py-2 rounded text-sm border transition"
            :class="isActive('tasks')
                ? activeClass
                : inactiveClass"
        >
            <i class="fa fa-list mr-1"></i>
            Tasks
        </Link>
        <Link
            :href="route('project.kanban', projectId)"
            class="px-4 py-2 rounded text-sm border transition"
            :class="isActive('board')
                ? activeClass
                : inactiveClass"
        >
            <i class="fa fa-columns mr-1"></i>
            Board view
        </Link>
        <Link
            :href="tabUrl('integrations')"
            class="px-4 py-2 rounded text-sm border transition"
            :class="isActive('integrations')
                ? activeClass
                : inactiveClass"
        >
            <i class="fa fa-plug mr-1"></i>
            Integrations
        </Link>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';

export default {
    components: {
        Link,
    },
    props: {
        projectId: {
            type: [Number, String],
            required: true,
        },
        activeTab: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            activeClass: 'bg-blue-50 border-blue-500 text-blue-700 dark:bg-blue-950/40 dark:border-blue-400 dark:text-blue-200',
            inactiveClass: 'border-gray-200 hover:bg-gray-50 dark:border-gray-600 dark:hover:bg-gray-800',
        };
    },
    methods: {
        tabUrl(tab) {
            return route('project.show', this.projectId) + '?tab=' + tab;
        },
        isActive(tab) {
            return this.activeTab === tab;
        },
    },
};
</script>
