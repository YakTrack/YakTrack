<template>
    <a
        :href="route('task.show', task.id)"
        class="block bg-white dark:bg-gray-800 rounded-lg shadow border dark:border-gray-700 p-4 hover:shadow-md transition-shadow cursor-grab active:cursor-grabbing"
    >
        <h3 class="font-medium text-gray-900 dark:text-gray-100 mb-2">
            {{ task.name }}
        </h3>
    </a>
</template>

<script>
export default {
    props: {
        task: {
            type: Object,
            required: true,
        },
    },
};
</script>
