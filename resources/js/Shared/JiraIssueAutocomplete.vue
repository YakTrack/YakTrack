<template>
    <div class="mb-6">
        <label :for="inputId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            {{ label }}
            <span v-if="required" class="text-red-500 ml-1">*</span>
        </label>

        <Combobox
            v-model="selectedIssue"
            nullable
            @update:model-value="onIssueSelected"
        >
            <div class="relative">
                <div class="relative">
                    <MagnifyingGlassIcon
                        class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                        aria-hidden="true"
                    />
                    <ComboboxInput
                        :id="inputId"
                        class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-10 pr-4 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder:text-gray-500"
                        :placeholder="placeholder"
                        autocomplete="off"
                        :display-value="() => modelValue"
                        @change="onInput"
                    />
                    <div
                        v-if="isSearching || isLoadingPreview"
                        class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2"
                    >
                        <i class="fa fa-spinner fa-spin text-gray-400" aria-hidden="true"></i>
                    </div>
                </div>

                <Transition
                    enter-active-class="transition duration-150 ease-out"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 translate-y-0"
                    leave-to-class="opacity-0 translate-y-1"
                >
                    <ComboboxOptions
                        v-if="showResults"
                        class="absolute z-20 mt-2 max-h-72 w-full overflow-auto rounded-md border border-gray-200 bg-white py-2 shadow ring-1 ring-black/5 dark:border-gray-600 dark:bg-gray-700 dark:ring-white/10"
                    >
                        <ComboboxOption
                            v-for="issue in results"
                            :key="issue.key"
                            v-slot="{ active }"
                            :value="issue"
                            as="template"
                        >
                            <li
                                class="flex cursor-pointer items-start gap-3 px-4 py-3 transition-colors"
                                :class="active ? 'bg-blue-50 dark:bg-blue-950/40' : ''"
                            >
                                <img
                                    v-if="issue.avatar_url"
                                    :src="issue.avatar_url"
                                    :alt="issue.issue_type ?? 'Issue'"
                                    class="mt-0.5 size-5 shrink-0 rounded"
                                />
                                <div
                                    v-else
                                    class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded bg-blue-100 text-[10px] font-bold text-blue-700 dark:bg-blue-900/60 dark:text-blue-300"
                                >
                                    J
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="inline-flex items-center rounded-md bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-800 dark:bg-blue-900/50 dark:text-blue-200">
                                            {{ issue.key }}
                                        </span>
                                        <span
                                            v-if="issue.issue_type"
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ issue.issue_type }}
                                        </span>
                                        <span
                                            v-if="issue.already_imported"
                                            class="inline-flex items-center rounded-md bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800 dark:bg-amber-900/40 dark:text-amber-200"
                                        >
                                            Linked to another task
                                        </span>
                                    </div>
                                    <p class="mt-1 truncate text-sm text-gray-700 dark:text-gray-200">
                                        {{ issue.summary }}
                                    </p>
                                </div>
                            </li>
                        </ComboboxOption>
                    </ComboboxOptions>
                </Transition>
            </div>
        </Combobox>

        <p
            v-if="searchError"
            class="mt-2 text-sm text-red-600 dark:text-red-400"
        >
            {{ searchError }}
        </p>
        <p
            v-else
            class="mt-2 text-sm text-gray-500 dark:text-gray-400"
        >
            Search Jira issues by key or summary, or enter a task name manually.
        </p>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Combobox, ComboboxInput, ComboboxOption, ComboboxOptions } from '@headlessui/vue'
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'
import debounce from 'lodash/debounce'

const props = defineProps({
    projectId: {
        type: Number,
        required: true,
    },
    modelValue: {
        type: String,
        default: '',
    },
    excludeTaskId: {
        type: Number,
        default: null,
    },
    label: {
        type: String,
        default: 'Task Name',
    },
    placeholder: {
        type: String,
        default: 'Search Jira issues or enter a task name',
    },
    required: {
        type: Boolean,
        default: false,
    },
    inputId: {
        type: String,
        default: 'jira-task-name',
    },
})

const emit = defineEmits(['update:modelValue', 'issue-populated'])

const results = ref([])
const selectedIssue = ref(null)
const isSearching = ref(false)
const isLoadingPreview = ref(false)
const searchError = ref('')
const hasSearched = ref(false)

const showResults = computed(() => props.modelValue.length >= 2 && (results.value.length > 0 || isSearching.value))

const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? ''

const searchIssues = debounce(async (searchQuery) => {
    if (searchQuery.length < 2) {
        results.value = []
        hasSearched.value = false
        searchError.value = ''
        return
    }

    isSearching.value = true
    searchError.value = ''

    try {
        const url = new URL(route('project.jira.issues.search', props.projectId), window.location.origin)
        url.searchParams.set('q', searchQuery)

        if (props.excludeTaskId) {
            url.searchParams.set('exclude_task_id', String(props.excludeTaskId))
        }

        const response = await fetch(url.toString(), {
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
        })

        const data = await response.json()

        if (!response.ok) {
            searchError.value = data.message ?? 'Could not search Jira issues.'
            results.value = []
            return
        }

        results.value = data.issues ?? []
        hasSearched.value = true
    } catch {
        searchError.value = 'Network error while searching Jira issues.'
        results.value = []
    } finally {
        isSearching.value = false
    }
}, 300)

const onInput = (event) => {
    emit('update:modelValue', event.target.value)
    selectedIssue.value = null
    searchIssues(event.target.value)
}

const loadPreview = async (issueKey) => {
    isLoadingPreview.value = true
    searchError.value = ''

    try {
        const url = new URL(route('project.jira.issues.preview', props.projectId), window.location.origin)
        url.searchParams.set('issue_key', issueKey)

        if (props.excludeTaskId) {
            url.searchParams.set('exclude_task_id', String(props.excludeTaskId))
        }

        const response = await fetch(url.toString(), {
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
        })

        const data = await response.json()

        if (!response.ok) {
            searchError.value = data.message ?? 'Could not load that Jira issue.'
            return null
        }

        return data.issue
    } catch {
        searchError.value = 'Network error while loading the Jira issue.'
        return null
    } finally {
        isLoadingPreview.value = false
    }
}

const truncateName = (name) => {
    if (name.length <= 255) {
        return name
    }

    return `${name.slice(0, 252)}…`
}

const onIssueSelected = async (issue) => {
    if (!issue?.key) {
        return
    }

    const preview = await loadPreview(issue.key)
    selectedIssue.value = null
    results.value = []

    if (!preview) {
        return
    }

    emit('issue-populated', {
        name: truncateName(preview.name ?? `${preview.key}: ${preview.summary}`),
        description: preview.description ?? '',
        jira_issue_key: preview.key,
    })
}

watch(
    () => props.projectId,
    () => {
        results.value = []
        selectedIssue.value = null
        searchError.value = ''
        hasSearched.value = false
    },
)
</script>
