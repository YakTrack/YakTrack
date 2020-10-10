<template>
    <duration
        :duration-in-seconds="secondsElapsedSinceStartTime"
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
            }
        },
        computed: {
            endTime() {
                return this.isRunning ? this.now : this.endedAt;
            },
            secondsElapsedSinceStartTime() {
                let secondsElapsedSinceLoaded = Math.floor((this.endTime - this.startedAt) / 1000);

                if (this.countDown) {
                    return this.initialTime - secondsElapsedSinceLoaded;
                }

                return this.initialTime + secondsElapsedSinceLoaded;
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
        created() {
            setInterval(() => this.$data.now = (new Date).getTime(), 1000);
        }
    }
</script>