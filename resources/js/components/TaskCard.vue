<template>
    <a
        :href="route('task.show', task.id)"
        class="block bg-white rounded-lg shadow border p-4 hover:shadow-md transition-shadow cursor-grab active:cursor-grabbing"
    >
        <h3 class="font-medium text-gray-900 mb-2">
            {{ task.name }}
        </h3>
        <div v-if="task.children_count > 0" class="flex items-center">
            <span class="text-xs bg-gray-100 text-gray-600 rounded px-2 py-1">
                <i class="fa fa-list mr-1"></i>
                {{ task.children_count }} subtask{{ task.children_count !== 1 ? 's' : '' }}
            </span>
        </div>
    </a>
</template>

<script>
export default {
    props: {
        task: {
            type: Object,
            required: true
        }
    }
}
</script>
