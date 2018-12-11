<template>
    <span class="text-grey-dark font-light p-2 border-green-light border rounded">
        {{ secondsElapsedSinceStartTime | durationForHumans }}
    </span>
</template>

<script>
    import DateTime from './../filters/DateTime';

    export default {
        props: {
            startedAt: {
                default: (new Date).getTime(),
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
            secondsElapsedSinceStartTime() {
                return Math.floor((this.now - this.startedAt) / 1000) + this.initialTime;
            },
        },
        created() {
            setInterval(() => this.$data.now = (new Date).getTime(), 1000);
        }
    }
</script>