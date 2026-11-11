<template>
    <div class="flex">
        <div v-if="session.task.project" class=" bg-grey-100 my-2 mx-0 p-2 flex-05">
            <div class="text-sm text-gray-700 dark:text-gray-300">
                <i class="fa fa-briefcase pr-1 text-gray-500 dark:text-gray-400"></i>
                {{ session.task.project.name }}
            </div>
            <div v-if="session.task.project.client" class="inline-block">
                <div class="bg-gray-300 dark:bg-gray-700 p-1 text-xs text-gray-500 dark:text-gray-400 rounded">
                    <i class="fa fa-user pr-1 text-gray-300 dark:text-gray-600"></i>
                    {{ session.task.project.client.name }}
                </div>
            </div>
        </div>
        <div v-else class="flex-1"></div>
        <div class="bg-grey-100 flex-1 mt-2 mr-2 mb-2 ml-0 p-2">
            <div class="text-gray-600 dark:text-gray-300 text-sm">
                {{ session.task.name }}
            </div>
            <div class="text-gray-500 dark:text-gray-400 text-xs">
                {{ session.task.description }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'session',
        ],
    }
</script>
