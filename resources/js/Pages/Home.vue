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
            <table
                class="bg-white rounded shadow"
                v-if="false"
            >
                <thead>
                    <tr>
                        <th class="text-center w-full bg-blue-200 mb-2" colspan="4">
                            <h3 class="text-blue-900 uppercase text-xl font-light"> Clients </h3>
                        </th>
                    </tr>
                </thead>
                <tbody
                    v-for="client in clients"
                    :key="client.id"
                >
                    <tr class="bg-blue-100">
                        <td class="pl-1 sm:pr-2" colspan="4">
                            <div class="uppercase text-blue-700 text-sm"> {{ client.name }} </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="px-1 text-gray-500 font-light text-sm text-left"></th>
                        <th class="px-1 text-gray-500 font-light text-sm text-right pr-6">
                            <span class="fa fa-bullseye mr-1"></span>
                        </th>
                        <th class="px-1 text-gray-500 font-light text-sm text-right pr-6">
                            <pre>+/-</pre>
                        </th>
                        <th class="px-1 text-gray-500 font-light text-sm text-right pr-6">
                            <span class="fa fa-stopwatch mr-1"></span>
                        </th>
                    </tr>
                    <tr
                        class=""
                        :key="client.id"
                    >
                        <!-- DATE COLUMN -->
                        <td class="pl-1 sm:pr-2">
                            <div class="uppercase text-gray-700 text-sm"> This Week </div>
                            <div class="font-thin text-gray-600 text-xs">
                                {{ dayjs(thisWeeksWorkSessions[0].date).format('Do MMM') }} - 
                                {{ dayjs(thisWeeksWorkSessions[thisWeeksWorkSessions.length - 1].date).format('Do MMM') }}
                            </div>
                        </td>

                        <!-- TARGET COLUMN -->
                        <td class="text-sm sm:text-base">
                            <div class="text-right font-mono">
                                <duration v-if="client.totalSecondsWeeklyTarget" :duration-in-seconds="client.totalSecondsWeeklyTarget" color="light-gray"></duration>
                                <span v-else class="text-gray-300 "> --:--:-- </span>
                            </div>
                        </td>

                        <!-- TIME REMAINING COLUMN -->
                        <td class="text-sm sm:text-base">
                            <div class="text-right font-mono" :class="client.totalTimeRemainingForWeeklyTarget != null ? 'text-gray-500' : ''">
                                <duration v-if="client.totalSecondsWeeklyTarget && !client.currentlyWorking" :duration-in-seconds="client.totalSecondsWeeklyTarget - client.totalSecondsWorkedThisWeek" :color="client.totalSecondsWorkedThisWeek > client.totalSecondsWeeklyTarget ? 'green' : 'light-gray'"></duration>
                                <timer v-else-if="client.totalSecondsWeeklyTarget" :initial-time="client.totalSecondsWeeklyTarget - client.totalSecondsWorkedThisWeek" :color="client.totalSecondsWorkedThisWeek - client.totalSecondsWeeklyTarget >= 0 ? 'green' : 'blue'" :count-down="true"/>
                                <span v-else class="text-gray-300">--:--:--</span>
                            </div>
                        </td>

                        <!-- TIME WORKED COLUMN -->
                        <td class="text-sm sm:text-base pr-1 sm:pr-2">
                            <div class="flex-1 text-right font-mono" :class="client.totalSecondsWorkedThisWeek > 0 ? 'text-gray-900' : ''">
                                {{ client.totalSecondsWorkedThisWeek }}
                                <timer v-if="currentClientName === client.name" :initial-time="client.totalSecondsWorkedThisWeek"/>
                                <span v-else class="font-mono">{{ client.sessionsThisWeek | totalDuration | durationForHumans }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr v-for="sprint in client.openSprints" :key="sprint.id">
                        <!-- DATE COLUMN -->
                        <td class="pl-1 sm:pr-2">
                            <div class="uppercase text-gray-700 text-sm"> {{ sprint.name }} </div>
                            <div class="font-thin text-gray-600 text-xs"> Open Sprint </div>
                        </td>

                        <!-- TARGET COLUMN -->
                        <td class="text-sm sm:text-base">
                            <div class="text-right font-mono">
                                <duration v-if="client.totalSecondsWeeklyTarget" :duration-in-seconds="client.totalSecondsWeeklyTarget" color="light-gray"></duration>
                                <span v-else class="text-gray-300 "> --:--:-- </span>
                            </div>
                        </td>

                        <!-- TIME REMAINING COLUMN -->
                        <td class="text-sm sm:text-base">
                            <div class="text-right font-mono" :class="client.totalTimeRemainingForWeeklyTarget != null ? 'text-gray-500' : ''">
                                <duration v-if="client.totalSecondsWeeklyTarget && !client.currentlyWorking" :duration-in-seconds="client.totalSecondsWeeklyTarget - client.totalSecondsWorkedThisWeek" :color="client.totalSecondsWorkedThisWeek > client.totalSecondsWeeklyTarget ? 'green' : 'light-gray'"></duration>
                                <timer v-else-if="client.totalSecondsWeeklyTarget" :initial-time="client.totalSecondsWeeklyTarget - client.totalSecondsWorkedThisWeek" :color="client.totalSecondsWorkedThisWeek - client.totalSecondsWeeklyTarget >= 0 ? 'green' : 'blue'" :count-down="true"/>
                                <span v-else class="text-gray-300">--:--:--</span>
                            </div>
                        </td>

                        <!-- TIME WORKED COLUMN -->
                        <td class="text-sm sm:text-base pr-1 sm:pr-2">
                            <div class="flex-1 text-right font-mono" :class="client.totalSecondsWorkedThisWeek > 0 ? 'text-gray-900' : ''">
                                <timer
                                    v-if="currentlyWorking && currentSession.sprint_id == sprint.id"
                                    :initial-time="sprint.sessions | totalDuration"
                                ></timer>
                                <span v-else class="font-mono">{{ sprint.sessions | totalDuration | durationForHumans }} </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="h-4" colspan="4">
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
