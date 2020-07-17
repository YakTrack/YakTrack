<template>
    <div class="px-3 inline-block btn-height font-light font-mono" :class="classes">
        <div class="flex h-full">
            <div class="self-center"> {{ secondsElapsedSinceStartTime | durationForHumans }} </div>
        </div>
    </div>
</template>

<script>
    import DateTime from './../filters/DateTime';

    export default {
        props: {
            startedAt: {
                default: (new Date).getTime(),
            },
            endedAt: {
                default: null,
            },
            initialTime: {
                default: 0,
            }
        },
        data() {
            return {
                dateTime: DateTime,
                createdAt: (new Date).getTime(),
                now: (new Date).getTime(),
            }
        },
        computed: {
            endTime() {
                return this.isRunning ? this.now : this.endedAt;
            },
            secondsElapsedSinceStartTime() {
                return Math.floor((this.endTime - this.startedAt) / 1000) + this.initialTime;
            },
            isRunning() {
                return this.endedAt === null;
            },
            classes() {
                return [
                    this.textColor,
                    this.fontSize,
                    this.border,
                    this.padding,
                ].join(' ');
            },
            textColor() {
                return this.isRunning ? 'text-green-700' : 'text-gray-700'
            },
            fontSize() {
                return this.isRunning ? '' : 'font-base'
            },
            border() {
                return this.isRunning ? 'bg-lue-100 rounded' : ''
            },
            padding() {
            },
        },
        created() {
            setInterval(() => this.$data.now = (new Date).getTime(), 1000);
        }
    }
</script>