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

        <div class="bg-white rounded shadow">
            <div class="text-center w-full bg-gray-200" colspan="4">
                <h3 class="text-indigo-90 uppercase text-xl font-light"> This Week </h3>
            </div>
            <div class="flex p-1">
                <button class="px-3 py-1 rounded" @click="billableStatus = 'billable'" :class="billableStatus === 'billable' ? 'bg-gray-200' : 'text-gray-500'">
                    Billable
                </button>
                <button class="px-3 py-1 rounded" @click="billableStatus = 'not_billable'" :class="billableStatus === 'not_billable' ? 'bg-gray-200' : 'text-gray-500'">
                    Not Billable
                </button>
            </div>

            <div class="flex flex-wrap">
                
                <table class="flex-1" :key="billableStatus">
                    <thead>
                        <tr class="bg-indigo-100">
                            <td class="pl-1 sm:pr-2" colspan="4">
                                <div class="uppercase text-indigo-700 text-sm"> All Clients </div>
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

                    </thead>
                    <tbody>
                        <tr
                            v-for="(dayOfWeek, index) in this_week.days"
                            class="h-12"
                            :class="dayOfWeek.is_today ? (dayOfWeek.currentlyWorking ? 'bg-green-100' : 'bg-blue-100') : ''"
                            :key="index"
                        >
                            <!-- DATE COLUMN -->
                            <td class="pl-1 sm:pr-2">
                                <div class="uppercase text-gray-700 text-sm"> {{ dayjs(dayOfWeek.date).format('ddd') }} </div>
                                <div class="font-thin text-gray-600 text-xs"> {{ dayjs(dayOfWeek.date).format('Do MMM') }} </div>
                            </td>

                            <!-- TARGET COLUMN -->
                            <td class="text-sm sm:text-base">
                                <div class="text-right font-mono">
                                    <duration v-if="dayOfWeek.totalSecondsTarget" :duration-in-seconds="dayOfWeek.totalSecondsTarget" color="light-gray"></duration>
                                    <span v-else class="text-gray-300 "> --:--:-- </span>
                                </div>
                            </td>

                            <!-- TIME REMAINING COLUMN -->
                            <td class="text-sm sm:text-base">
                                <div class="text-right font-mono" :class="dayOfWeek.totalTimeRemainingForTarget != null ? 'text-gray-500' : ''">
                                    <duration v-if="dayOfWeek.totalSecondsTarget && !dayOfWeek.currentlyWorking" :duration-in-seconds="dayOfWeek.totalSecondsTarget - dayOfWeek.totalSecondsWorked" :color="dayOfWeek.totalSecondsWorked > dayOfWeek.totalSecondsTarget ? 'green' : 'light-gray'"></duration>
                                    <timer v-else-if="dayOfWeek.totalSecondsTarget" :initial-time="dayOfWeek.totalSecondsTarget - dayOfWeek.totalSecondsWorked" :color="dayOfWeek.totalSecondsWorked - dayOfWeek.totalSecondsTarget >= 0 ? 'green' : 'blue'" :count-down="true"/>
                                    <span v-else class="text-gray-300">--:--:--</span>
                                </div>
                            </td>

                            <!-- TIME WORKED COLUMN -->
                            <td class="text-sm sm:text-base pr-1 sm:pr-2">
                                <div class="flex-1 text-right font-mono" :class="dayOfWeek[billableStatus].actual > 0 ? 'text-gray-900' : ''">
                                    <timer v-if="dayOfWeek[billableStatus].is_active" :initial-time="dayOfWeek[billableStatus].actual"/>
                                    <duration v-else-if="dayOfWeek.is_today || dayOfWeek[billableStatus].actual > 0" :duration-in-seconds="dayOfWeek[billableStatus].actual"/>
                                    <span v-else class="text-gray-300">--:--:--</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="h-4" colspan="4">
                            </td>
                        </tr>
                        <tr>
                            <th class="text-left uppercase text-gray-700 text-sm sm:text-base font-bold p-0 pl-1 bg-indigo-100">
                                Total
                            </th>

                            <!-- Total Weekly Target -->
                            <th class="text-right text-sm sm:text-base p-0 bg-indigo-100">
                                <duration :duration-in-seconds="this_week.billable.target"/>
                            </th>

                            <!-- Total Remaining For Week -->
                            <th class="text-right text-sm sm:text-base p-0 bg-indigo-100">
                                <timer v-if="currentlyWorking" :initial-time="totalSecondsRemainingForTargetsThisWeek" :count-down="true"></timer>
                                <duration v-else :duration-in-seconds="totalSecondsRemainingForTargetsThisWeek"/>
                            </th>

                            <!-- Total Worked For Week -->
                            <th class="text-right text-sm sm:text-base p-0 pr-1 sm:pr-2 bg-indigo-100">
                                <timer v-if="currentlyWorking" :initial-time="this_week.billable.actual" color="indigo"></timer>
                                <strong v-else class="font-mono"> {{ thisWeeksTotal }} </strong>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>

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
    }
</script>