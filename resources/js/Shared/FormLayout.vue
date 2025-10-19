<template>
    <div class="card">
        <div class="card-body">
            <form @submit.prevent="$emit('submit')" class="space-y-6">
                <slot></slot>
                
                <!-- Form Actions -->
                <div class="flex justify-end gap-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <slot name="actions">
                        <Link
                            v-if="cancelUrl"
                            :href="cancelUrl"
                            class="px-6 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md font-medium hover:bg-gray-400 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="processing"
                        >
                            {{ processing ? submitLoadingText : submitText }}
                        </button>
                    </slot>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    cancelUrl: {
        type: String,
        default: ''
    },
    submitText: {
        type: String,
        default: 'Save'
    },
    submitLoadingText: {
        type: String,
        default: 'Saving...'
    },
    processing: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['submit'])
</script>
