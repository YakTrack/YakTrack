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
        <table class="bg-white rounded shadow w-full sm:w-1/2">
            <thead>
                <tr>
                    <th class="text-center w-full bg-indigo-100 mb-2" colspan="4">
                        <h3 class="text-gray-600 uppercase text-xl font-light"> This Week </h3>
                    </th>
                </tr>
                <tr>
                    <th class="px-1 text-gray-500 font-light text-sm text-left"></th>
                    <th class="px-1 text-gray-500 font-light text-sm text-right pr-6">
                        <icon class="fa fa-bullseye mr-1"></icon>
                    </th>
                    <th class="px-1 text-gray-500 font-light text-sm text-right pr-6">
                        <pre>+/-</pre>
                    </th>
                    <th class="px-1 text-gray-500 font-light text-sm text-right pr-6">
                        <icon class="fa fa-stopwatch mr-1"></icon>
                    </th>
                </tr>

            </thead>
            <tbody>
                <tr
                    v-for="(dayOfWeek, index) in thisWeeksWorkSessions"
                    class=""
                    :class="dayOfWeek.isToday ? (dayOfWeek.currentlyWorking ? 'bg-green-100' : 'bg-blue-100') : ''"
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
                            <icon v-if="dayOfWeek.totalSecondsTarget" class="fa fa-bullseye text-gray-400 hidden"></icon>
                            <duration v-if="dayOfWeek.totalSecondsTarget" :duration-in-seconds="dayOfWeek.totalSecondsTarget" color="light-gray"></duration>
                            <span v-else class="text-gray-300 "> --:--:-- </span>
                        </div>
                    </td>

                    <!-- TIME REMAINING COLUMN -->
                    <td class="text-sm sm:text-base">
                        <div class="text-right font-mono" :class="dayOfWeek.totalTimeRemainingForTarget != null ? 'text-gray-500' : ''">
                            <icon v-if="dayOfWeek.totalSecondsTarget" class="fa fa-clock hidden"></icon>
                            <duration v-if="dayOfWeek.totalSecondsTarget && !dayOfWeek.currentlyWorking" :duration-in-seconds="dayOfWeek.totalSecondsTarget - dayOfWeek.totalSecondsWorked" :color="dayOfWeek.totalSecondsWorked > dayOfWeek.totalSecondsTarget ? 'green' : 'light-gray'"></duration>
                            <timer v-else-if="dayOfWeek.totalSecondsTarget" :initial-time="dayOfWeek.totalSecondsTarget - dayOfWeek.totalSecondsWorked" :color="dayOfWeek.totalSecondsWorked - dayOfWeek.totalSecondsTarget >= 0 ? 'green' : 'blue'" :count-down="true"/>
                            <span v-else class="text-gray-300">--:--:--</span>
                        </div>
                    </td>

                    <!-- TIME WORKED COLUMN -->
                    <td class="text-sm sm:text-base pr-1 sm:pr-2">
                        <div class="flex-1 text-right font-mono" :class="dayOfWeek.totalSecondsWorked > 0 ? 'text-gray-900' : ''">
                            <icon v-if="dayOfWeek.isToday || dayOfWeek.totalSecondsWorked > 0" class="fa fa-stopwatch hidden"></icon>
                            <timer v-if="dayOfWeek.currentlyWorking" :initial-time="dayOfWeek.totalSecondsWorked"/>
                            <duration v-else-if="dayOfWeek.isToday || dayOfWeek.totalSecondsWorked > 0" :duration-in-seconds="dayOfWeek.totalSecondsWorked"/>
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
                        <duration :duration-in-seconds="thisWeeksWorkSessions.reduce((c, d) => c + d.totalSecondsTarget, 0)"/>
                    </th>

                    <!-- Total Remaining For Week -->
                    <th class="text-right text-sm sm:text-base p-0 bg-indigo-100">
                        <timer v-if="currentlyWorking" :initial-time="totalSecondsRemainingForTargetsThisWeek" :count-down="true"></timer>
                        <duration v-else :duration-in-seconds="totalSecondsRemainingForTargetsThisWeek"/>
                    </th>

                    <!-- Total Worked For Week -->
                    <th class="text-right text-sm sm:text-base p-0 pr-1 sm:pr-2 bg-indigo-100">
                        <timer v-if="currentlyWorking" :initial-time="totalSecondsThisWeek" color="indigo"></timer>
                        <strong v-else class="font-mono"> {{ thisWeeksTotal }} </strong>
                    </th>
                </tr>
            </tfoot>
        </table>

        <div class="grid pt-4">
            <div v-for="client in clients" :key="client.id" class="rounded shadow bg-white">
                <div class="p-4">
                    <h3 class="text-xl"> {{ client.name }} </h3>
                    <h4 class="mt-4 uppercase text-gray-800"> This Week </h4>
                    <div class="flex mt-3 bg-gray-100 p-2 rounded">
                        <div class="flex-1 text-gray-700 font-light"> Total Time </div>
                        <div class="flex-1 text-right">
                            <timer v-if="currentlyWorking && currentClientName == client.name" :initial-time="client.sessionsThisWeek | totalDuration"></timer>
                            <span v-else class=" font-mono">{{ client.sessionsThisWeek | totalDuration | durationForHumans }}</span>
                        </div>
                    </div>
                    <div v-for="sprint in client.openSprints" :key="sprint.id">
                        <h4 class="mt-4 uppercase"> {{ sprint.name }} </h4>
                        <div class="flex mt-3 bg-gray-100 p-2 rounded">
                            <div class="flex-1 text-gray-700 font-light"> Total Time </div>
                            <div class="flex-1 text-right">
                                <timer
                                    v-if="currentlyWorking && currentSession.sprint_id == sprint.id"
                                    :initial-time="sprint.sessions | totalDuration"
                                ></timer>
                                <span v-else class=" font-mono">{{ sprint.sessions | totalDuration | durationForHumans }} </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            'thisWeeksWorkSessions',
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
            }
        },
    }
</script>