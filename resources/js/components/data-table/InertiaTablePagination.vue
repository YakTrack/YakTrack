<template>
    <div
        v-if="paginator.links && paginator.links.length"
        class="flex flex-col gap-4 border-t border-gray-200 bg-gray-50/50 px-4 py-4 dark:border-gray-800 dark:bg-gray-900/40 sm:flex-row sm:items-center sm:justify-between"
    >
        <div class="flex flex-wrap items-center gap-3">
            <p class="text-sm text-gray-700 dark:text-gray-300">
                <span class="font-medium">{{ paginator.from ?? 0 }}</span>
                –
                <span class="font-medium">{{ paginator.to ?? 0 }}</span>
                of
                <span class="font-medium">{{ paginator.total }}</span>
            </p>
            <label v-if="perPageOptions.length" class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span class="whitespace-nowrap">Rows</span>
                <select
                    class="rounded-md border border-gray-300 bg-white py-1.5 pl-2 pr-8 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    :value="perPage"
                    @change="$emit('per-page', Number($event.target.value))"
                >
                    <option v-for="n in perPageOptions" :key="n" :value="n">
                        {{ n }}
                    </option>
                </select>
            </label>
        </div>
        <nav class="flex flex-wrap items-center justify-end gap-1" aria-label="Pagination">
            <template v-for="link in paginator.links" :key="link.label">
                <Link
                    v-if="link.url"
                    :href="link.url"
                    preserve-state
                    preserve-scroll
                    class="relative inline-flex min-w-[2.25rem] items-center justify-center rounded-md border px-3 py-2 text-sm font-medium transition-colors"
                    :class="
                        link.active
                            ? 'z-10 border-indigo-500 bg-indigo-50 text-indigo-600 dark:border-indigo-400 dark:bg-indigo-950/50 dark:text-indigo-300'
                            : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'
                    "
                    v-html="link.label"
                />
                <span
                    v-else
                    class="relative inline-flex min-w-[2.25rem] cursor-not-allowed items-center justify-center rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-400 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-500"
                    v-html="link.label"
                />
            </template>
        </nav>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    paginator: {
        type: Object,
        required: true,
    },
    perPage: {
        type: Number,
        default: 15,
    },
    perPageOptions: {
        type: Array,
        default: () => [10, 15, 25, 50],
    },
})

defineEmits(['per-page'])
</script>
