<template>
    <layout>
        <template slot="breadcrumbs">
            <breadcrumbs
                :breadcrumbs="[
                    {title: 'Home'},
                ]"
            ></breadcrumbs>
        </template>
        <template slot="title"> Home </template>

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
    import breadcrumbs from '@/Shared/Breadcrumbs';
    import layout from '@/Shared/Layout';
    import timer from '@/components/Timer';
    import duration from '@/components/Duration';
    import sessionsSummaryTable from '@/components/SessionsSummaryTable';

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
