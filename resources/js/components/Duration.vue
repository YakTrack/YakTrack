<template>
    <div class="inline-block btn-height font-light font-mono" :class="classes">
        <div class="flex h-full">
            <div class="self-center whitespace-pre">{{ $filters.durationForHumans(durationInSeconds) }}</div>
        </div>
    </div>
</template>

<script>
    import DateTime from '@/filters/DateTime.js';

    export default {
        props: {
            durationInSeconds: {
                type: Number,
                default: 0,
            },
            color: {
                type: String,
                default: 'gray',
            },
            fontSize: {
                type: String,
                default: 'default',
            },
        },
        computed: {
            classes() {
                return [
                    this.textColor,
                    this.fontSizeClasses,
                ].join(' ');
            },
            textColor() {
                return {
                    blue: 'text-blue-700 dark:text-blue-300',
                    green: 'text-green-700 dark:text-green-300',
                    gray: 'text-gray-700 dark:text-gray-300',
                    'light-gray': 'text-gray-600 dark:text-gray-300',
                }[this.color]
            },
            fontSizeClasses() {
                return {
                    base: 'font-base',
                    default: '',
                }[this.fontSize]
            },
        },
    }
</script>