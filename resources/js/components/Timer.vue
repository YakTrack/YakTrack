<template>
    <duration
        :duration-in-seconds="durationInSeconds"
        :color="textColor"
    ></duration>
</template>

<script>
    import Duration from './../components/Duration';
    import DateTime from './../filters/DateTime';

    export default {
        components: {
            duration: Duration,
        },
        props: {
            startedAt: {
                default: (new Date).getTime(),
            },
            endedAt: {
                default: null,
            },
            initialTime: {
                default: 0,
            },
            countDown: {
                type: Boolean,
                default: false,
            },
            isPaused: {
                type: Boolean,
                default: false,
            },
            color: {
                type: String,
                default: null,
            },
        },
        data() {
            return {
                dateTime: DateTime,
                createdAt: (new Date).getTime(),
                now: (new Date).getTime(),
                poller: null,
            }
        },
        computed: {
            durationInSeconds() {
                let offset = this.isRunning ? this.secondsElapsedSinceCreatedAt : 0;

                return this.initialTime + offset * (this.countDown ? -1 : 1);
            },
            endTime() {
                return this.isRunning ? this.now : this.endedAt;
            },
            secondsElapsedSinceCreatedAt() {
                return Math.floor((this.now - this.createdAt) / 1000);
            },
            isRunning() {
                if (this.isPaused) {
                    return false;
                }

                return this.endedAt === null;
            },
            classes() {
                return [
                    this.textColor,
                    this.fontSize,
                    this.border,
                ].join(' ');
            },
            textColor() {
                if (this.color) {
                    return this.color;
                }

                if (this.countDown) {
                    return 'gray';
                }

                return this.isRunning ? 'green' : 'gray';
            },
            fontSize() {
                return this.isRunning ? '' : 'font-base'
            },
        },
        mounted() {
            this.poller = setInterval(() => this.$data.now = (new Date).getTime(), 1000);
        },
        destroyed() {
            clearInterval(this.poller);  
        },
    }
</script>