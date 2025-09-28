<template>
    <layout>
        <template #breadcrumbs>
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home'},
                ]"
            ></breadcrumbs>
        </template>
        <template #title> Home </template>

        <sessions-summary-table
            :this-week="this_week"
            :total-seconds-remaining-for-targets-this-week="totalSecondsRemainingForTargetsThisWeek"
            :this-weeks-total="thisWeeksTotal"
            :currently-working="currentlyWorking"
        />
    </layout>
</template>

<script>
    import dayjs from 'dayjs';
    import breadcrumbs from '@/Shared/Breadcrumbs.vue';
    import layout from '@/Shared/Layout.vue';
    import timer from '@/components/Timer.vue';
    import duration from '@/components/Duration.vue';
    import sessionsSummaryTable from '@/components/SessionsSummaryTable.vue';

    export default {
        props: [
            'this_week',
            'totalSecondsRemainingForTargetsThisWeek',
            'thisWeeksTotal',
            'clients',
            'currentlyWorking',
            'currentSession',
            'totalSecondsThisWeek',
            'currentClientName',
        ],
        components: {
            breadcrumbs: breadcrumbs,
            sessionsSummaryTable: sessionsSummaryTable,
            layout: layout,
            timer: timer,
            duration: duration,
        },
        data() {
            return {
                dayjs: dayjs,
                billableStatus: 'billable',
            }
        },
        mounted() {
          document.title = 'YakTrack'
        },
    }
</script>
