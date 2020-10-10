<template>
    <div class="inline-block btn-height font-light font-mono" :class="classes">
        <div class="flex h-full">
            <div class="self-center whitespace-pre">{{ durationInSeconds | durationForHumans }}</div>
        </div>
    </div>
</template>

<script>
    import DateTime from './../filters/DateTime';

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
                    blue: 'text-blue-700',
                    green: 'text-green-700',
                    gray: 'text-gray-700',
                    'light-gray': 'text-gray-600',
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