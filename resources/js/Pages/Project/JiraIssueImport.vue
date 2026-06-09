<template>
    <div class="space-y-6">
        <div v-if="!previewIssue">
            <label :for="inputId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Import issue as task
            </label>
            <Combobox
                v-model="selectedSuggestion"
                nullable
                @update:model-value="onSuggestionSelected"
            >
                <div class="relative">
                    <div class="relative">
                        <MagnifyingGlassIcon
                            class="pointer-events-none absolute left-3.5 top-1/2 size-5 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                            aria-hidden="true"
                        />
                        <ComboboxInput
                            :id="inputId"
                            class="block w-full rounded-md border border-gray-300 bg-white py-2 pl-11 pr-4 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 transition-colors duration-200 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 dark:placeholder:text-gray-500"
                            placeholder="Search by issue key or summary…"
                            autocomplete="off"
                            :display-value="() => query"
                            @change="onQueryInput"
                        />
                        <div
                            v-if="isSearching"
                            class="pointer-events-none absolute right-3.5 top-1/2 -translate-y-1/2"
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
                                :disabled="issue.already_imported"
                                as="template"
                            >
                                <li
                                    class="flex cursor-pointer items-start gap-3 px-4 py-3 transition-colors"
                                    :class="[
                                        active && !issue.already_imported
                                            ? 'bg-indigo-50 dark:bg-indigo-950/40'
                                            : '',
                                        issue.already_imported
                                            ? 'cursor-not-allowed opacity-60'
                                            : '',
                                    ]"
                                >
                                    <img
                                        v-if="issue.avatar_url"
                                        :src="issue.avatar_url"
                                        :alt="issue.issue_type ?? 'Issue'"
                                        class="mt-0.5 size-5 shrink-0 rounded"
                                    />
                                    <div
                                        v-else
                                        class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded bg-indigo-100 text-[10px] font-bold text-indigo-700 dark:bg-indigo-900/60 dark:text-indigo-300"
                                    >
                                        J
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-flex items-center rounded-md bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200">
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
                                                Already imported
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
                v-else-if="query.length >= 2 && !isSearching && results.length === 0 && hasSearched"
                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
            >
                No matching issues found. Try a different key or summary.
            </p>
            <p
                v-else
                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
            >
                Type at least 2 characters to search your Jira site.
            </p>
            <p
                v-if="serverError"
                class="mt-2 text-sm text-red-600 dark:text-red-400"
            >
                {{ serverError }}
            </p>
        </div>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="opacity-0 scale-[0.98]"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-150 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-[0.98]"
        >
            <div
                v-if="previewIssue"
                class="overflow-hidden rounded border border-indigo-200/80 bg-gradient-to-br from-white to-indigo-50/50 shadow ring-1 ring-indigo-100 dark:border-indigo-800/60 dark:from-gray-800 dark:to-indigo-950/30 dark:ring-indigo-900/40"
            >
                <div class="border-b border-indigo-100/80 px-6 py-4 dark:border-indigo-900/50">
                    <div class="flex items-center gap-2 text-sm font-medium text-indigo-700 dark:text-indigo-300">
                        <CheckCircleIcon class="size-5" aria-hidden="true" />
                        Confirm import
                    </div>
                </div>

                <div class="space-y-4 px-6 py-5">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded bg-indigo-600 px-3 py-1 text-sm font-bold tracking-wide text-white shadow-sm">
                            {{ previewIssue.key }}
                        </span>
                        <span
                            v-if="selectedSuggestion?.issue_type"
                            class="inline-flex items-center rounded border border-gray-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-600 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300"
                        >
                            {{ selectedSuggestion.issue_type }}
                        </span>
                    </div>

                    <h3 class="text-lg font-semibold leading-snug text-gray-900 dark:text-gray-100">
                        {{ previewIssue.summary }}
                    </h3>

                    <p
                        v-if="previewIssue.description"
                        class="whitespace-pre-wrap text-sm leading-relaxed text-gray-600 dark:text-gray-400"
                    >
                        {{ previewIssue.description }}
                    </p>
                    <p
                        v-else
                        class="text-sm italic text-gray-400 dark:text-gray-500"
                    >
                        No description on this issue.
                    </p>

                    <div
                        v-if="previewIssue.already_imported"
                        class="flex items-start gap-3 rounded border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-800/60 dark:bg-amber-950/30"
                    >
                        <ExclamationTriangleIcon class="mt-0.5 size-5 shrink-0 text-amber-600 dark:text-amber-400" aria-hidden="true" />
                        <p class="text-sm text-amber-800 dark:text-amber-200">
                            This issue has already been imported as a task in this project.
                        </p>
                    </div>

                    <div
                        v-else
                        class="rounded border border-gray-200 bg-white/80 px-4 py-3 text-sm text-gray-600 dark:border-gray-700 dark:bg-gray-900/50 dark:text-gray-400"
                    >
                        A new Yaktrack task will be created with this issue's title and description.
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 border-t border-indigo-100/80 bg-white/60 px-6 py-4 dark:border-indigo-900/50 dark:bg-gray-900/30">
                    <button
                        type="button"
                        class="btn btn-default"
                        :disabled="isLoadingPreview || processing"
                        @click="clearSelection"
                    >
                        Choose a different issue
                    </button>
                    <button
                        type="button"
                        class="btn btn-blue"
                        :disabled="previewIssue.already_imported || isLoadingPreview || processing"
                        @click="confirmImport"
                    >
                        <i v-if="processing" class="fa fa-spinner fa-spin mr-2" aria-hidden="true"></i>
                        Create task from Jira issue
                    </button>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { Combobox, ComboboxInput, ComboboxOption, ComboboxOptions } from '@headlessui/vue'
import {
    CheckCircleIcon,
    ExclamationTriangleIcon,
    MagnifyingGlassIcon,
} from '@heroicons/vue/20/solid'
import debounce from 'lodash/debounce'

const props = defineProps({
    projectId: {
        type: Number,
        required: true,
    },
    serverError: {
        type: String,
        default: '',
    },
})

const page = usePage()
const inputId = `jira-issue-search-${props.projectId}`

const query = ref('')
const results = ref([])
const selectedSuggestion = ref(null)
const previewIssue = ref(null)
const isSearching = ref(false)
const isLoadingPreview = ref(false)
const searchError = ref('')
const hasSearched = ref(false)

const processing = computed(() => page.props.processing ?? false)

const showResults = computed(() => {
    return query.value.length >= 2 && (results.value.length > 0 || isSearching.value)
})

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

const onQueryInput = (event) => {
    query.value = event.target.value
    selectedSuggestion.value = null
    previewIssue.value = null
    searchIssues(query.value)
}

const loadPreview = async (issueKey) => {
    isLoadingPreview.value = true
    searchError.value = ''

    try {
        const url = new URL(route('project.jira.issues.preview', props.projectId), window.location.origin)
        url.searchParams.set('issue_key', issueKey)

        const response = await fetch(url.toString(), {
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
        })

        const data = await response.json()

        if (!response.ok) {
            searchError.value = data.message ?? 'Could not load that Jira issue.'
            previewIssue.value = null
            return
        }

        previewIssue.value = data.issue
    } catch {
        searchError.value = 'Network error while loading the Jira issue.'
        previewIssue.value = null
    } finally {
        isLoadingPreview.value = false
    }
}

const onSuggestionSelected = async (issue) => {
    if (!issue || issue.already_imported) {
        return
    }

    query.value = issue.key
    results.value = []
    await loadPreview(issue.key)
}

const clearSelection = () => {
    selectedSuggestion.value = null
    previewIssue.value = null
    query.value = ''
    results.value = []
    hasSearched.value = false
    searchError.value = ''
}

const confirmImport = () => {
    if (!previewIssue.value || previewIssue.value.already_imported) {
        return
    }

    router.post(route('project.jira.import', props.projectId), {
        issue_key: previewIssue.value.key,
    })
}

watch(
    () => props.serverError,
    (error) => {
        if (error) {
            previewIssue.value = null
            selectedSuggestion.value = null
        }
    },
)
</script>
