<template>
    <div>
        <p
            v-if="isCreate"
            class="text-sm text-gray-600 dark:text-gray-400"
        >
            Choose a project, then add the task details below.
        </p>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Project
                <span v-if="isCreate" class="text-red-500 ml-1">*</span>
            </label>
            <multi-select
                v-model="selectedProject"
                :options="projects"
                label="name"
                placeholder="Select a project"
            />
        </div>

        <jira-issue-autocomplete
            v-if="selectedProject?.jira_integration"
            v-model="form.name"
            :project-id="selectedProject.id"
            :exclude-task-id="task?.id ?? null"
            label="Task Name"
            placeholder="Search Jira issues or enter a task name"
            :required="true"
            @issue-populated="onJiraIssuePopulated"
        />

        <form-field
            v-else
            v-model="form.name"
            type="text"
            label="Task Name"
            placeholder="Enter task name"
            :required="true"
        />

        <div v-if="selectedProject && selectedProject.task_statuses" class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Status
            </label>
            <multi-select
                v-model="selectedStatus"
                :options="selectedProject.task_statuses"
                label="name"
                placeholder="Select a status"
            >
                <template slot="option" slot-scope="props">
                    <div class="flex items-center">
                        <div
                            :style="{ backgroundColor: props.option.color }"
                            class="w-3 h-3 rounded-full mr-2"
                        ></div>
                        {{ props.option.name }}
                    </div>
                </template>
                <template slot="singleLabel" slot-scope="props">
                    <div class="flex items-center">
                        <div
                            :style="{ backgroundColor: props.option.color }"
                            class="w-3 h-3 rounded-full mr-2"
                        ></div>
                        {{ props.option.name }}
                    </div>
                </template>
            </multi-select>
        </div>

        <form-field
            v-model="form.description"
            type="textarea"
            label="Description"
            placeholder="Describe this task"
            help="Optional"
            :rows="3"
        />
    </div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import multiSelect from 'vue-multiselect'
import formField from '@/Shared/FormField.vue'
import JiraIssueAutocomplete from '@/Shared/JiraIssueAutocomplete.vue'

const props = defineProps({
    projects: {
        type: Array,
        required: true,
    },
    tasks: {
        type: Array,
        default: () => [],
    },
    task: {
        type: Object,
        default: null,
    },
    prefillProjectId: {
        type: Number,
        default: null,
    },
    focusedClientId: {
        type: Number,
        default: null,
    },
})

const isCreate = computed(() => props.task == null)

const findInitialProject = () => {
    if (props.task?.project_id) {
        return props.projects.find((project) => project.id == props.task.project_id) ?? null
    }

    if (props.prefillProjectId) {
        return props.projects.find((project) => project.id == props.prefillProjectId) ?? null
    }

    if (props.focusedClientId) {
        return props.projects.find((project) => project.client_id == props.focusedClientId) ?? null
    }

    return null
}

const findInitialStatus = () => {
    if (!props.task?.status_id) {
        return null
    }

    const project = props.projects.find((item) => item.id == props.task.project_id)
    if (!project?.task_statuses) {
        return null
    }

    return project.task_statuses.find((status) => status.id == props.task.status_id) ?? null
}

const selectedProject = ref(findInitialProject())
const selectedStatus = ref(findInitialStatus())
const form = reactive({
    name: props.task?.name ?? '',
    description: props.task?.description ?? '',
    jira_issue_key: props.task?.jira_issue_key ?? null,
})

const escapeRegExp = (string) => string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')

const onJiraIssuePopulated = (issue) => {
    form.name = issue.name
    form.description = issue.description
    form.jira_issue_key = issue.jira_issue_key
}

watch(selectedProject, (newProject, oldProject) => {
    selectedStatus.value = null

    if (oldProject && newProject?.id !== oldProject?.id) {
        form.jira_issue_key = null
    }

    if (!isCreate.value || !newProject?.task_code_prefix || form.name) {
        return
    }

    const projectTasks = props.tasks.filter((task) => task.project_id === newProject.id)
    const pattern = new RegExp(`^${escapeRegExp(newProject.task_code_prefix)}-(\\d+):`)
    const numbers = projectTasks
        .map((task) => {
            const match = task.name.match(pattern)
            return match ? parseInt(match[1], 10) : null
        })
        .filter((num) => num !== null)
    const nextNumber = numbers.length > 0 ? Math.max(...numbers) + 1 : 1
    form.name = `${newProject.task_code_prefix}-${String(nextNumber).padStart(4, '0')}: `
})

const payload = () => ({
    name: form.name,
    description: form.description,
    project_id: selectedProject.value?.id ?? null,
    status_id: selectedStatus.value?.id ?? null,
    jira_issue_key: form.jira_issue_key,
})

defineExpose({
    payload,
})
</script>
