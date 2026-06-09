<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    { title: 'Home', url: route('home') },
                    { title: 'Tasks', url: route('task.index') },
                    { title: 'Create Task' },
                ]"
            />
        </template>
        <template #title>Create Task</template>

        <form-layout
            :cancel-url="route('task.index')"
            submit-text="Create"
            submit-loading-text="Creating..."
            :processing="processing"
            @submit="submit"
        >
            <task-form
                ref="taskFormRef"
                :projects="projects"
                :tasks="tasks"
                :prefill-project-id="prefill_project_id"
            />
        </form-layout>
    </layout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import breadcrumbs from '@/Shared/Breadcrumbs.vue'
import formLayout from '@/Shared/FormLayout.vue'
import layout from '@/Shared/Layout.vue'
import TaskForm from '@/Pages/Task/TaskForm.vue'

defineProps({
    projects: {
        type: Array,
        required: true,
    },
    tasks: {
        type: Array,
        default: () => [],
    },
    prefill_project_id: {
        type: Number,
        default: null,
    },
})

const taskFormRef = ref(null)

const processing = computed(() => router.processing)

const submit = () => {
    router.post(route('task.store'), taskFormRef.value.payload())
}
</script>
