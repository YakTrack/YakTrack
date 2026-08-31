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

        <div
            v-if="focusedClient"
            class="mb-4 flex flex-wrap items-center gap-x-2 gap-y-1 rounded-md bg-blue-50 px-4 py-3 text-sm text-blue-800 dark:bg-blue-900/30 dark:text-blue-200"
        >
            <span>Focusing on <strong>{{ focusedClient.name }}</strong>.</span>
            <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800 dark:bg-blue-800/50 dark:text-blue-100">
                Targets shown are across all clients
            </span>
        </div>

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
        computed: {
            focusedClient() {
                return this.$page.props.focusedClient ?? null;
            },
        },
        mounted() {
          document.title = 'YakTrack'
        },
    }
</script>
