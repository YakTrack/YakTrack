<template>
    <span class="font-mono text-gray-600">
        <span :class="!time ? 'text-gray-400' : ''">{{ parseInt(hours) < 10 ? '&nbsp;' : '' }}{{ hours }}:{{ minutes }}</span><span :class="time ? 'text-gray-600' : 'text-gray-400'">:</span><span :class="time ? 'text-gray-600' : 'text-gray-400'">{{ seconds }}</span>
        <span :class="time ? 'text-gray-500' : 'text-gray-400'">{{ amOrPm }}</span>
    </span>
</template>

<script>

    import dayjs from 'dayjs';

    export default {
        props: [
            'time',
        ],
        computed: {
            datetime() {
                return this.time ? dayjs(this.time) : {
                    format: key => '--',
                };
            },
            hours() {
                return this.datetime.format('h');
            },
            minutes() {
                return this.datetime.format('mm');
            },
            seconds() {
                return this.datetime.format('ss');
            },
            amOrPm() {
                return this.datetime.format('a');
            },
        },
    }
</script>