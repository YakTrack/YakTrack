<template>
    <div
        class="flex flex-col gap-4 border-b border-gray-200 bg-gray-50/80 p-4 dark:border-gray-800 dark:bg-gray-900/60 sm:flex-row sm:items-end sm:justify-between"
    >
        <div class="relative max-w-md flex-1">
            <MagnifyingGlassIcon
                class="pointer-events-none absolute left-3 top-1/2 size-5 -translate-y-1/2 text-gray-400 dark:text-gray-500"
                aria-hidden="true"
            />
            <input
                id="data-table-search"
                type="search"
                autocomplete="off"
                :value="modelValue"
                :placeholder="searchPlaceholder"
                class="block w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:placeholder:text-gray-500 dark:focus:border-indigo-400 dark:focus:ring-indigo-400/20"
                @input="onInput"
            />
        </div>
        <div class="flex flex-wrap items-end gap-3">
            <slot name="filters"></slot>
            <button
                v-if="showClear"
                type="button"
                class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                @click="$emit('clear')"
            >
                Clear
            </button>
        </div>
    </div>
</template>

<script setup>
import { MagnifyingGlassIcon } from '@heroicons/vue/20/solid'

defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    searchPlaceholder: {
        type: String,
        default: 'Search…',
    },
    showClear: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue', 'search', 'clear'])

const onInput = (event) => {
    emit('update:modelValue', event.target.value)
    emit('search')
}
</script>
