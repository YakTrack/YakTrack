<template>
    <a
        :href="route('task.show', task.id)"
        class="block bg-white rounded-lg shadow border p-4 hover:shadow-md transition-shadow cursor-grab active:cursor-grabbing"
    >
        <h3 class="font-medium text-gray-900 mb-2">
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
