<template>
    <div class="flex">
        <div v-if="session.task.project" class=" bg-grey-lightest my-2 mx-0 p-2 flex-05">
            <div class="text-sm text-grey-darker">
                <i class="fa fa-briefcase pr-1 text-grey"></i>
                {{ session.task.project.name }}
            </div>
            <div v-if="session.task.project.client" class="inline-block">
                <div class="bg-grey-lighter p-1 text-xs text-grey rounded">
                    <i class="fa fa-user pr-1 text-grey-light"></i>
                    {{ session.task.project.client.name }}
                </div>
            </div>
        </div>
        <div v-else class="flex-1"></div>
        <div class="bg-grey-lightest flex-1 mt-2 mr-2 mb-2 ml-0 p-2">
            <div class="text-grey-dark text-sm">
                {{ session.task.name }}
            </div>
            <div class="text-grey text-xs">
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
