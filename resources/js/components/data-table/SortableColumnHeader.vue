<template>
    <th
        :class="[
            'px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400',
            align === 'right' ? 'text-right' : 'text-left',
        ]"
        scope="col"
    >
        <button
            type="button"
            class="group inline-flex items-center gap-1 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
            :class="align === 'right' ? 'ml-auto' : ''"
            @click="$emit('sort', column)"
        >
            <span>{{ label }}</span>
            <span class="flex flex-col text-[10px] leading-none text-gray-400 dark:text-gray-500">
                <ChevronUpIcon
                    class="size-3 -mb-0.5"
                    :class="
                        sort === column && direction === 'asc'
                            ? 'text-indigo-600 dark:text-indigo-400'
                            : 'opacity-30 group-hover:opacity-60'
                    "
                />
                <ChevronDownIcon
                    class="size-3"
                    :class="
                        sort === column && direction === 'desc'
                            ? 'text-indigo-600 dark:text-indigo-400'
                            : 'opacity-30 group-hover:opacity-60'
                    "
                />
            </span>
        </button>
    </th>
</template>

<script setup>
import { ChevronDownIcon, ChevronUpIcon } from '@heroicons/vue/20/solid'

defineProps({
    label: {
        type: String,
        required: true,
    },
    column: {
        type: String,
        required: true,
    },
    sort: {
        type: String,
        required: true,
    },
    direction: {
        type: String,
        required: true,
    },
    align: {
        type: String,
        default: 'left',
    },
})

defineEmits(['sort'])
</script>
